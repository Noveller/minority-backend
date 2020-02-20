<?php

class PressController extends WP_REST_Controller {
    public function __construct()
    {
        $this->namespace = 'namespace/v1';
        $this->rest_base = 'press';
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->rest_base, array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_items')
            )
        ));
    }

    public function get_item_schema()
    {
        return $this->add_additional_fields_schema(array(
            // This tells the spec of JSON Schema we are using which is draft 4.
            '$schema'              => 'http://json-schema.org/draft-04/schema#',
            // The title property marks the identity of the resource.
            'title'                => 'press',
            'type'                 => 'object',
            // In JSON Schema you can specify object properties in the properties attribute.
            'properties'           => array(
                'id' => array(
                    'description'  => esc_html__( 'Unique identifier for the object.', 'my-textdomain' ),
                    'type'         => 'integer',
                    'context'      => array( 'view', 'edit', 'embed' ),
                    'readonly'     => true,
                ),
                'content' => array(
                    'description'  => esc_html__( 'The content for the object.', 'my-textdomain' ),
                    'type'         => 'string',
                ),
                'short_description' => array(
                    'type' => 'string'
                ),
                'image' => array(
                    'type' => 'string'
                )
            ),
        ));
    }

    public function prepare_item_for_response($post, $request)
    {
        $post_data = array();

        $schema = $this->get_item_schema();

        // We are also renaming the fields to more understandable names.
        if ( isset( $schema['properties']['id'] ) ) {
            $post_data['id'] = (int) $post->ID;
        }

        if ( isset( $schema['properties']['content'] ) ) {
            $post_data['content'] = apply_filters( 'the_content', $post->post_content, $post );
        }

        if (isset($schema['properties']['short_description'])) {
            $post_data['short_description'] = strip_tags(get_post_meta($post->ID, 'short_description', true));
        }

        if(isset($schema['properties']['image'])) {
            $post_data['image'] = get_the_post_thumbnail_url($post->ID);
        }

        return rest_ensure_response( $post_data );
    }


    public function get_items($request)
    {
        $args = array(
            'post_per_page' => 5,
        );
        $posts = get_posts(array( 'post_type' => 'press' ));


        $data = array();

        if ( empty( $posts ) ) {
            return rest_ensure_response( $data );
        }

        foreach ( $posts as $post ) {
            $response = $this->prepare_item_for_response( $post, $request );
            $data[] = $this->prepare_response_for_collection( $response );
        }

        // Return all of our comment response data.
        return rest_ensure_response( $data );
    }

}