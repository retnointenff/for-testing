<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Scan_Factory extends CI_Controller
{

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$content_data = array(
			'title' => 'New Form Scan',
			'base_url' => base_url(),
			'page' => static::class,
			'csrf_token_name' => $this->security->get_csrf_token_name(),
			'csrf_hash' => $this->security->get_csrf_hash()
		);
		$this->load->view('header', $content_data);
		$this->load->view('form', $content_data);
		$this->load->view('js', $content_data);
		$this->load->view('footer', $content_data);
	}

	public function uploadFileScan()
	{
		$data = $this->input->post();
		if (isset($data['data'])) {
			$files = $data['data'];
			if (preg_match('/^data:image\/(\w+);base64,/', $files, $type)) {
				$files = substr($files, strpos($files, ',') + 1);
				$type = strtolower($type[1]); // jpg, png, gif

				if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
					throw new \Exception('invalid image type');
				}
				$files = str_replace(' ', '+', $files);
				$files = base64_decode($files);

				if ($files === false) {
					throw new \Exception('base64_decode failed');
				}
			} else {
				throw new \Exception('did not match data URI with type data');
			}

			$file_name = date('YmdHis') . rand(0, 100);
			$path = './storage/scan_doc/temp/';
			if (!file_exists($path)) {
				mkdir($path, 0777, true);
			}
			file_put_contents("${path}{$file_name}.{$type}", $files);

			if (file_exists("${path}{$file_name}.{$type}")) {
				// array_push($arrayImages, "${path}{$file_name}.{$type}");
			}
			echo json_encode("{$file_name}.{$type}");
		} else {
			echo json_encode('file not upload');
		}
	}
}
