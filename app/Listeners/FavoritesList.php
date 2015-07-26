<?php 

namespace SimpleFavorites\Listeners;

use SimpleFavorites\Entities\User\UserFavorites;

/**
* Return an HTML formatted list of user's favorites
* (For use in replacing cached content with AJAX injected content)
*/
class FavoritesList extends AJAXListenerBase
{
	/**
	* Form Data
	*/
	protected $data;

	/**
	* HTML formatted list
	*/
	private $list;

	public function __construct()
	{
		$this->setData();
		$this->setList();
		$this->response(array('status'=>'success', 'list' => $this->list));
	}

	/**
	* Set the Form Data
	*/
	private function setData()
	{
		$this->data['user_id'] = ( $_POST['userid'] !== '' ) ? intval($_POST['userid']) : null;
		$this->data['site_id'] = ( isset($_POST['siteid']) ) ? intval($_POST['siteid']) : null;
		$this->data['links'] =  ( isset($_POST['links']) && $_POST['links'] == 'true' ) ? true : false;
		$this->data['include_buttons'] =  ( isset($_POST['include_buttons']) && $_POST['include_buttons'] == 'true' ) ? true : false;
	}

	/**
	* Set the formatted list
	*/
	private function setList()
	{
		$favorites = new UserFavorites($this->data['user_id'], $this->data['site_id'], $this->data['links']);
		$this->list = $favorites->getFavoritesList($this->data['include_buttons']);
	}	

}