<?php

namespace DT\Home\Sources;

/**
 * Base repository class for interacting with a queried data-feed
 * likely pulled from a database, API or some other source.
 * Some examples might be a database, a Post Type,
 * a JSON file, or an API.
 *
 * @param array $items Array of item.
 */
abstract class Source
{
    /**
     * Returns all raw data.
     *
     * @return array An array containing all the raw data.
     */
    abstract public function raw( array $params = [] ): array;

    /**
     * Save the item.
     */
    abstract public function save( $items, array $options = [] ): bool;

    /**
     * Retrieve the sort key from the given items.
     *
     * @return string The sort key if found in the first item of $items
     */
    public function sort_key()
    {
        return 'sort';
    }

    /**
     * Returns the key used to find the item.
     *
     * @return string The key used to find the item.
     */
    public function find_key()
    {
        return 'slug';
    }

    /**
     * Returns formatted item.
     *
     * @param array $params The parameters for filtering item.
     *
     * @return array An array containing the formatted item.
     */
    public function formatted( array $params = [] ): array
    {
        return $this->format(
            $this->raw( $params )
        );
    }

    /**
     * Returns all item.
     *
     * @return array An array containing all the items.
     */
    public function all( array $params = [] ): array
    {
        return $this->formatted( $params );
    }

    /**
     * Fetches data for saving.
     *
     * @param array $params An array of parameters for fetching data.
     * @return array An array containing the fetched data formatted for saving.
     */
    public function fetch_for_save( array $params = [] ): array
    {
        return $this->formatted( $params );
    }

    /**
     * Formats the given item.
     *
     * @param array $items The item to be formatted.
     *
     * @return array The formatted item.
     */
    protected function format( array $items ): array
    {
        $items = array_map(function ( $item ) {
            return $this->format_item( $item );
        }, $items);

        return $this->sort( $items );
    }


    /**
     * Formats the item data.
     *
     * @param array $item The item data to format.
     *
     * @return array The formatted item data.
     */
    protected function format_item( array $item ): array
    {
        return $item;
    }

    /**
     * Sorts the item array by the 'sort' field in ascending order.
     *
     * @param array $items The item array to sort.
     *
     * @return array The item array sorted by the 'sort' field in ascending order.
     */
    public function sort( array $items, array $params = [] ): array
    {
        $key = $params['key'] ?? $this->sort_key();
        $asc = $params['asc'] ?? true;
        $reset = $params['reset'] ?? false;

        if ( !isset( $items[0][$key] ) ) {
            return $items;
        }

        usort($items, function ( $a, $b ) use ( $key, $asc ) {
            if ( !isset( $a[$key], $b[$key] ) || ( $a[$key] === $b[$key] ) ) {
                return 0;
            }

            if ( $asc ) {
                return ( $a[$key] < $b[$key] ) ? -1 : 1;
            } else {
                return ( $a[$key] > $b[$key] ) ? -1 : 1;
            }
        });

        if ( $reset ) {
            $count = 0;

            $items = array_map(function ( $item ) use ( $key, &$count ) {
                $item[$key] = $count++;

                return $item;
            }, $items);
        }

        return $items;
    }

    /**
     * Find an element in the array by its find key
     *
     * @param string|int $find_key_value The value of the element to find
     * @param array $params Optional parameters to apply when retrieving item
     * @return array|null The filtered array containing the element(s) with the provided slug or null if not found
     */
    public function find( $find_key_value, array $params = [] )
    {
        return $this->first(array_filter($this->all( $params ), function ( $item ) use ( $find_key_value ) {
            return $item[$this->find_key()] === $find_key_value;
        }));
    }

    /**
     * Find the index of an element in the array by its find key
     *
     * @param string|int $value The value of the element to find
     * @return int|false The index of the element with the provided slug, or false if not found
     */
    public function find_index( $value, array $params = [] )
    {
        $items = $this->all( $params );

        return array_search( $value, array_column( $items, $this->find_key() ) );
    }

    /**
     * Set a value for a specific key in an element
     *
     * @param string $find_key_value The find key value of the element to modify
     * @param string $key The key of the value to set
     * @param mixed $value The new value to set
     * @param array $params Optional parameters to apply when retrieving item
     * @param array $save_params Optional parameters to apply when saving the updated array
     * @return bool|array False if the element with the provided slug does not exist, otherwise returns the updated array after saving
     */
    public function set( string $find_key_value, string $key, $value, array $params = [], array $save_params = [] )
    {
        $items = $this->raw( $params );
        $index = array_search( $find_key_value, array_column( $items, $this->find_key() ) );
		if ( $index === false ) {
            return false;
        }
        $items[$index][$key] = $value;
        $this->save( $items, $save_params );
        return $this->value( $find_key_value, $key );
    }

    /**
     * Toggle the value of a specific key in the element with the provided slug
     *
     * @param string $find_key_value The slug of the element
     * @param string $key The key to toggle the value of
     * @param array $params Optional parameters to apply when retrieving item
     * @return mixed The new value of the toggled key
     */
    public function toggle( string $find_key_value, string $key, array $params = [], array $save_params = [] )
    {
        $value = $this->value( $find_key_value, $key, $params );
        $this->set( $find_key_value, $key, !$value, $params, $save_params );
        return $this->value( $find_key_value, $key, $params );
    }

    /**
     * Get the value for a specific key of an element by its slug
     *
     * @param string $find_key_value The slug of the element to find
     * @param string $key The key of the value to retrieve
     * @return mixed|null The value associated with the given key of the element with the provided slug,
     *                    or null if no element is found with the given slug
     */
    public function value( string $find_key_value, string $key, $params = [] )
    {
        $items = $this->raw( $params );
        $index = array_search( $find_key_value, array_column( $items, $this->find_key() ) );
        if ( $index === false ) {
            return null;
        }
        return $items[$index][$key] ?? null;
    }

    /**
     * Checks if the item is visible.
     *
     * @param array $item The item data.
     *
     * @return bool True if the item is visible, false otherwise.
     */
    public function is_visible( array $item ): bool
    {
        return !$item['is_hidden'];
    }

    /**
     * Filters and retrieves hidden item.
     *
     * @param array $items Array of item.
     * @return array Hidden item.
     */
    public function hidden( array $items ): array
    {
        return array_filter($items, function ( $item ) {
            return !$this->is_visible( $item );
        });
    }

    /**
     * Returns the first element of the given array or null if the array is empty.
     *
     * @param array $items The input array.
     * @return mixed|null The first element of the array or null if the array is empty.
     */
    public function first( array $items )
    {
        return !empty( $items ) ? $items[array_key_first( $items )] : null;
    }


    /**
     * Retrieves all deleted item.
     *
     * @param array $params Optional parameters to apply when retrieving item.
     * @return array All deleted item data.
     */
    public function deleted( array $params = [] ): array
    {
        $params = array_merge( $params, [ 'filter' => false ] );
        $items = $this->all( $params );
        $filtered = array_filter($items, function ( $item ) {
            return $item['is_deleted'] === true;
        });
        return $filtered;
    }

    /**
     * Retrieves all undeleted item.
     *
     * @param array $params Additional parameters for filtering.
     * @return array Undeleted item data.
     */
    public function undeleted( array $params = [] ): array
    {
        $items = $this->all( $params );
        $filtered = array_filter($items, function ( $item ) {
            return $item['is_deleted'] !== true;
        });
        return $filtered;
    }

    /**
     * Updates an item by slug.
     *
     * @param string $find_key_value The slug of the item.
     * @param array $item The updated item data.
     * @param array $params Optional parameters to apply when retrieving item.
     * @param array $save_params Optional parameters to apply when saving the updated item.
     * @return bool|array Returns false if the item is not found, otherwise returns the updated item data.
     */
    public function update( string $find_key_value, array $item, array $params = [], $save_params = [] )
    {
        $items = $this->fetch_for_save( $params );
        $index = array_search( $find_key_value, array_column( $items, $this->find_key() ) );
        if ( $index === false ) {
            return false;
        }
        $items[$index] = $item;
        return $this->save( $items, $save_params );
    }

    /**
     * Soft Deletes an item.
     *
     * @param string $slug The slug of the item to be deleted.
     * @param array $params Optional. Additional parameters for the deletion. Default is an empty array.
     * @param array $save_params Optional. Additional parameters for saving the changes. Default is an empty array.
     *
     * @return bool Returns true if the item was successfully deleted.
     */
    public function delete( string $slug, array $params = [], $save_params = [] )
    {
        $items = $this->fetch_for_save( $params );
        $index = array_search( $slug, array_column( $items, $this->find_key() ) );
        if ( $index === false ) {
            return false;
        }
        unset( $items[$index] );
        return $this->save( $items, $save_params );
    }
}
