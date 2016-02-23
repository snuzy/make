<?php
/**
 * @package Make
 */

/**
 * Class MAKE_Choices_Manager
 *
 * An object for defining and managing choice sets.
 *
 * @since x.x.x.
 */
class MAKE_Choices_Manager extends MAKE_Util_Modules implements MAKE_Choices_ManagerInterface, MAKE_Util_LoadInterface {
	/**
	 * An associative array of required modules.
	 *
	 * @since x.x.x.
	 *
	 * @var array
	 */
	protected $dependencies = array(
		'error'         => 'MAKE_Error_CollectorInterface',
		'compatibility' => 'MAKE_Compatibility_MethodsInterface',
	);

	/**
	 * The collection of choice sets.
	 *
	 * @since x.x.x.
	 *
	 * @var array
	 */
	protected $choice_sets = array();

	/**
	 * Indicator of whether the load routine has been run.
	 *
	 * @since x.x.x.
	 *
	 * @var bool
	 */
	protected $loaded = false;

	/**
	 * Load data files.
	 *
	 * @since x.x.x.
	 *
	 * @return void
	 */
	public function load() {
		if ( true === $this->is_loaded() ) {
			return;
		}

		// Load the default choices definitions
		$file = dirname( __FILE__ ) . '/definitions/choices.php';
		if ( is_readable( $file ) ) {
			include $file;
		}

		// Loading has occurred.
		$this->loaded = true;

		/**
		 * Action: Fires at the end of the choices object's load method.
		 *
		 * This action gives a developer the opportunity to add or modify choice sets
		 * and run additional load routines.
		 *
		 * @since x.x.x.
		 *
		 * @param MAKE_Choices_Manager    $choices    The choices object that has just finished loading.
		 */
		do_action( 'make_choices_loaded', $this );
	}

	/**
	 * Check if the load routine has been run.
	 *
	 * @since x.x.x.
	 *
	 * @return bool
	 */
	public function is_loaded() {
		return $this->loaded;
	}

	/**
	 * Add choice sets to the collection.
	 *
	 * Each choice set is an item in an associative array.
	 * The item's array key is the set ID. The item value is another
	 * associative array that contains individual choices where the key
	 * is the HTML option value and the value is the HTML option label.
	 *
	 * Example:
	 * array(
	 *     'horizontal-alignment' => array(
	 *         'left'   => __( 'Left', 'make' ),
	 *         'center' => __( 'Center', 'make' ),
	 *         'right'  => __( 'Right', 'make' ),
	 *     ),
	 * )
	 *
	 * @since x.x.x.
	 *
	 * @param          $sets         Array of choice sets to add.
	 * @param  bool    $overwrite    True overwrites an existing choice set with the same ID.
	 *
	 * @return bool                  True if addition was successful, false if there was an error.
	 */
	public function add_choice_sets( $sets, $overwrite = false ) {
		// Make sure we're not doing it wrong.
		if ( 'make_choices_loaded' !== current_action() && did_action( 'make_choices_loaded' ) ) {
			$backtrace = debug_backtrace();

			$this->compatibility()->doing_it_wrong(
				__FUNCTION__,
				__( 'This function should only be called during or before the <code>make_choices_loaded</code> action.', 'make' ),
				'1.7.0',
				$backtrace
			);

			return false;
		}

		$sets = (array) $sets;
		$existing_sets = $this->choice_sets;
		$new_sets = array();
		$return = true;

		// Validate each choice set before adding it.
		foreach ( $sets as $set_id => $choices ) {
			$set_id = sanitize_key( $set_id );

			// Choice set isn't valid.
			if ( ! is_array( $choices ) ) {
				$this->error()->add_error( 'make_choices_set_not_valid', sprintf( __( 'The "%s" choice set can\'t be added because it\'s not an array.', 'make' ), esc_html( $set_id ) ) );
				$return = false;
			}
			// Choice set already exists, overwriting disabled.
			else if ( isset( $existing_sets[ $set_id ] ) && true !== $overwrite ) {
				$this->error()->add_error( 'make_choices_set_already_exists', sprintf( __( 'The "%s" choice set can\'t be added because it already exists.', 'make' ), esc_html( $set_id ) ) );
				$return = false;
			}
			// Add a new choice set.
			else {
				$new_sets[ $set_id ] = $choices;
			}
		}

		// Add the valid new choices sets to the existing choices array.
		if ( ! empty( $new_sets ) ) {
			$this->choice_sets = array_merge( $existing_sets, $new_sets );
		}

		return $return;
	}

	/**
	 * Remove choice sets from the collection.
	 *
	 * @since x.x.x.
	 *
	 * @param  array|string    $set_ids    The array of choice sets to remove, or 'all'.
	 *
	 * @return bool                        True if removal was successful, false if there was an error.
	 */
	public function remove_choice_sets( $set_ids ) {
		if ( 'all' === $set_ids ) {
			// Clear the entire settings array.
			$this->choice_sets = array();
			return true;
		}

		$return = true;

		foreach ( (array) $set_ids as $set_id ) {
			if ( isset( $this->choice_sets[ $set_id ] ) ) {
				unset( $this->choice_sets[ $set_id ] );
			} else {
				$this->error()->add_error( 'make_choices_cannot_remove', sprintf( __( 'The "%s" choice set can\'t be removed because it doesn\'t exist.', 'make' ), esc_html( $set_id ) ) );
				$return = false;
			}
		}

		return $return;
	}

	/**
	 * Getter for the choice sets that ensures the load routine has run first.
	 *
	 * @since x.x.x.
	 *
	 * @return array
	 */
	protected function get_choice_sets() {
		if ( ! $this->is_loaded() ) {
			$this->load();
		}

		return $this->choice_sets;
	}

	/**
	 * Check if a choice set exists.
	 *
	 * @since x.x.x.
	 *
	 * @param string $set_id
	 *
	 * @return bool
	 */
	public function choice_set_exists( $set_id ) {
		$choice_sets = $this->get_choice_sets();
		return isset( $choice_sets[ $set_id ] );
	}

	/**
	 * Get a particular choice set, using the set ID.
	 *
	 * @since x.x.x.
	 *
	 * @param  string    $set_id    The ID of the choice set to retrieve.
	 *
	 * @return array                The array of choices.
	 */
	public function get_choice_set( $set_id ) {
		$choice_set = array();

		if ( $this->choice_set_exists( $set_id ) ) {
			$choice_sets = $this->get_choice_sets();
			$choice_set = $choice_sets[ $set_id ];
		}

		/**
		 * Filter: Modify the choices in a particular choice set.
		 *
		 * @since x.x.x.
		 *
		 * @param array     $choice_set    The array of choices in the choice set.
		 * @param string    $set_id        The ID of the choice set.
		 */
		return apply_filters( 'make_choices_get_choice_set', $choice_set, $set_id );
	}

	/**
	 * Get the label of an individual choice in a choice set.
	 *
	 * @since x.x.x.
	 *
	 * @param  string    $value     The array key representing the value of the choice.
	 * @param  string    $set_id    The ID of the choice set.
	 *
	 * @return string               The choice label, or empty string if not a valid choice.
	 */
	public function get_choice_label( $value, $set_id ) {
		if ( ! $this->is_valid_choice( $value, $set_id ) ) {
			$this->error()->add_error( 'make_choices_not_valid_choice', sprintf( __( '"%1$s" is not a valid choice in the "%2$s" set.', 'make' ), esc_html( $value ), esc_html( $set_id ) ) );
			return '';
		}

		// Get the choice set.
		$choices = $this->get_choice_set( $set_id );

		/**
		 * Filter: Modify the label for a particular choice value.
		 *
		 * @since x.x.x.
		 *
		 * @param string    $label     The label for the choice.
		 * @param mixed     $choice    The value for the choice.
		 * @param string    $set_id    The ID of the set that the choice belongs to.
		 */
		return apply_filters( 'make_choices_get_choice_label', $choices[ $value ], $value, $set_id );
	}

	/**
	 * Determine if a value is a valid choice.
	 *
	 * @since x.x.x.
	 *
	 * @param  string    $value     The array key representing the value of the choice.
	 * @param  string    $set_id    The ID of the choice set.
	 *
	 * @return bool                 True if the choice exists in the set.
	 */
	public function is_valid_choice( $value, $set_id ) {
		$choices = $this->get_choice_set( $set_id );
		return isset( $choices[ $value ] );
	}

	/**
	 * Sanitize a value from a list of allowed values in a choice set.
	 *
	 * @since x.x.x.
	 *
	 * @param  mixed     $value      The value given to sanitize.
	 * @param  string    $set_id     The ID of the choice set to search for the given value.
	 * @param  mixed     $default    The value to return if the given value is not valid.
	 *
	 * @return mixed                 The sanitized value.
	 */
	public function sanitize_choice( $value, $set_id, $default = '' ) {
		if ( true === $this->is_valid_choice( $value, $set_id ) ) {
			return $value;
		} else {
			return $default;
		}
	}
}