<?php 
namespace Favorites\Listeners;

use Favorites\Entities\Favorite\Favorite;

class FavoriteButton extends AJAXListenerBase
{
	public function __construct()
	{
		parent::__construct();
		$this->setFormData();
		$this->updateFavorite();
	}

	/**
	* Set Form Data
	*/
	private function setFormData()
	{
		$this->data['postid'] = intval(sanitize_text_field($_POST['postid']));
		$this->data['siteid'] = intval(sanitize_text_field($_POST['siteid']));
		$this->data['status'] = ( $_POST['status'] == 'active') ? 'active' : 'inactive';
		$this->data['groupid'] = ( isset($_POST['groupid']) && $_POST['groupid'] !== '' ) ? intval($_POST['groupid']) : 1;
	}

	/**
	* Update the Favorite
	*/
	private function updateFavorite()
	{
		try {
			$this->beforeUpdateAction();
			$favorite = new Favorite;
			$favorite->update($this->data['postid'], $this->data['status'], $this->data['siteid'], $this->data['groupid']);
			$this->afterUpdateAction();
			$this->response([
				'status' => 'success', 
				'favorite_data' => [
					'id' => $this->data['postid'], 
					'siteid' => $this->data['siteid'], 
					'status' => $this->data['status'],
					'groupid' => $this->data['groupid'],
					'save_type' => $favorite->saveType()
				],
				'favorites' => $this->user_repo->formattedFavorites($this->data['postid'], $this->data['siteid'], $this->data['status'])
			]);
		} catch ( \Exception $e ){
			return $this->sendError($e->getMessage());
		}
	}

	/**
	* Before Update Action
	* Provides hook for performing actions before a favorite
	*/
	private function beforeUpdateAction()
	{
		$user = ( is_user_logged_in() ) ? get_current_user_id() : null;
		do_action('favorites_before_favorite', $this->data['postid'], $this->data['status'], $this->data['siteid'], $user);
	}

	/**
	* After Update Action
	* Provides hook for performing actions after a favorite
	*/
	private function afterUpdateAction()
	{
		$user = ( is_user_logged_in() ) ? get_current_user_id() : null;
		do_action('favorites_after_favorite', $this->data['postid'], $this->data['status'], $this->data['siteid'], $user);
	}
}