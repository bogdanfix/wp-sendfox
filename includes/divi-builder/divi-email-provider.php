<?php
/**
 * Wrapper for SendFox API
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */

class ET_Core_API_Email_SendFox extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.sendfox.com';

	/**
	 * @inheritDoc
	 */
	public $name = 'SendFox';

	/**
	 * @inheritDoc
	 */
	public $slug = 'sendfox';

	/**
	 * @inheritDoc
	 */
	public $uses_oauth = FALSE;

	public function __construct( $owner, $account_name, $api_key = '' )
	{
		parent::__construct( $owner, $account_name, $api_key );

		$this->_maybe_set_custom_headers();
	}

	protected function _maybe_set_custom_headers()
	{
		if( empty( $this->custom_headers ) && isset( $this->data['api_key'] ) )
		{
			$this->custom_headers = array( 
        		'Authorization' => 'Bearer ' . $this->data['api_key'],
			);
		}
	}

	public function get_account_fields()
	{
		return array(
			'api_key' => array(
				'label' => esc_html__( 'API Key', 'et_core' ),
			),
		);
	}

	public function get_data_keymap( $keymap = array() )
	{
		$keymap = array(
			'list'       => array(
				'list_id'           => 'id',
				'name'              => 'name',
				'subscribers_count' => 'subscribed_contacts_count',
			),
			'subscriber' => array(
				'email'      => 'email',
				'name'       => 'first_name',
				'last_name'  => 'last_name',
			),
		);

		return parent::get_data_keymap( $keymap );
	}

	public function fetch_subscriber_lists()
	{
		if( empty( $this->data['api_key'] ) )
		{
			return $this->API_KEY_REQUIRED;
		}

		if( empty( $this->custom_headers ) )
		{
			$this->_maybe_set_custom_headers();
		}

		/**
		 * The maximum number of subscriber lists to request from SendFox's API.
		 * One page = 10 lists.
		 *
		 * @since 2.0.0
		 *
		 * @param int $max_lists
		 */

		$max_lists = (int) apply_filters( 'et_core_api_email_sendfox_max_lists', 20 );

		$url = "{$this->BASE_URL}/lists";

		$this->prepare_request( $url, 'GET', FALSE, array() );

		$this->response_data_key = 'data';

		$result = parent::fetch_subscriber_lists();

		$lists = $this->data['lists'];

		if( $max_lists > 10 )
		{
			$count_pages = absint( $max_lists / 10 );

			for( $i = 2; $i <= $count_pages; $i++ )
			{
				$url = "{$this->BASE_URL}/lists?page={$i}";

				$this->prepare_request( $url, 'GET', FALSE, array() );

				$this->response_data_key = 'data';

				$result = parent::fetch_subscriber_lists();

				foreach( $this->data['lists'] as $id => $list )
				{
					if( !empty( $list['list_id'] ) )
					{
						$lists[ $id ] = $list;
					}
				}
			}
		}

		$this->data['lists'] = $lists;

		$this->save_data();

		return $result;
	}

	public function subscribe( $args, $url = '' )
	{
		$list_id   = $args['list_id'];

		$args      = $this->transform_data_to_provider_format( $args, 'subscriber', array( 'list_id' ) );

		$url       = "{$this->BASE_URL}/contacts";

		$email     = $args['email_address'];

		$err       = esc_html__( 'An error occurred, please try later.', 'et_core' );

		$ip_address = 'true' === self::$_->array_get( $args, 'ip_address', 'true' ) ? et_core_get_ip_address() : '0.0.0.0';

		$args['lists'] = array( intval( $list_id ) );

		unset( $args['list_id'] );

		if( empty( $this->custom_headers ) )
		{
			$this->_maybe_set_custom_headers();
		}

		$this->prepare_request( $url, 'POST', FALSE, $args, TRUE );

		$result = parent::subscribe( $args, $url );

		if( 'success' !== $result )
		{
			$result = $err;
		}

		return $result;
	}
}
