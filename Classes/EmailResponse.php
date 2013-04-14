<?php
 /*
 *  PayPal Email response
 *
 *
 *  
 *
 *  @package    PHP-PayPal-IPN
 *  @author     Michel Dourneau
 *  @version    1.0.0
 */
class EmailResponse
{
	/**
	 * @var string Template name for response.
	 */
	private $template;

	/**
	 * @var IpnListener
	 */
	private $ipnListener;

	/**
	 *
	 *
	 */
	public function __construct(IpnListener $ipnListener, $template = 'exemple.html')
	{
		try {
			$this->ipnListener  = $ipnListener;
			$this->template 		= $template;
		} catch(Exception $e) {
			error_log($e->getMessage());
		}
	}

	/**
	 *
	 *
	 */
	public function sendConfirmationClient()
	{
		if ($template = $this->isTemplateExist()) {
			$template = $this->parseTemplate($template);
			try {
				$sujet   = sprintf('Commande %s sur %s', $this->ipnListener->getPostData('txn_id'), $_SERVER['HTTP_HOST']);
				$message = $template;
				$destinataire = $this->ipnListener->getPostData('payer_email');
				$headers  = sprintf("From: \"%s\"<no-reply@%s>\n", $_SERVER['HTTP_HOST'], $_SERVER['HTTP_HOST']);
				$headers .= sprintf("Reply-To: no-reply@%s\n", $_SERVER['HTTP_HOST']);
				$headers .= "Content-Type: text/html; charset=\"utf-8\"";				
				mail($destinataire,$sujet,$message,$headers);
			} catch (Exception $e) {
				error_log($e->getMessage());
			}
		}
	}

	/**
	 *
	 *
	 */
	public function parseTemplate($template)
	{
		$template   = file_get_contents($template);
		$post_data  = $this->ipnListener->getPostDatas();
		if (!empty($post_data)) {
			foreach ($post_data as $key => $value) {
				$template = str_replace("{% $key %}", "$value", $template);
			}
		}

		return $template;
	}

	/**
	 *
	 *
	 */
	private function isTemplateExist()
	{
		if (file_exists(sprintf(dirname(__FILE__).'/../email_templates/%s', $this->template))){
			return sprintf(dirname(__FILE__).'/email_templates/%s', $this->template);
		}
	}
}