<?php namespace KODESKPLUGIN\Element;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Border;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Plugin;

/**
 * Elementor button widget.
 * Elementor widget that displays a button with the ability to control every
 * aspect of the button design.
 *
 * @since 1.0.0
 */
class Workspaces_V2 extends Widget_Base {

    /**
     * Get widget name.
     * Retrieve button widget name.
     *
     * @since  2.0.0
     * @access public
     * @return string Widget name.
     */
    public function get_name() {
        return 'kodesk_workspaces_v2';
    }

    /**
     * Get widget title.
     * Retrieve button widget title.
     *
     * @since  2.0.0
     * @access public
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Workspaces V2', 'kodesk' );
    }

    /**
     * Get widget icon.
     * Retrieve button widget icon.
     *
     * @since  2.0.0
     * @access public
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'fa fa-briefcase';
    }

    /**
     * Get widget categories.
     * Retrieve the list of categories the button widget belongs to.
     * Used to determine where to display the widget in the editor.
     *
     * @since  2.0.0
     * @access public
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'kodesk' ];
    }

    /**
     * Register button widget controls.
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  2.0.0
     * @access protected
     */
    protected function _register_controls() {
        $this->start_controls_section(
            'workspaces_v2',
            [
                'label' => esc_html__( 'Workspaces V2', 'kodesk' ),
            ]
        );
		$this->add_control(
			'subtitle',
			[
				'label'       => __( 'Sub Title', 'kodesk' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'title',
			[
				'label'       => __( 'Title', 'kodesk' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$this->add_control(
            'btn_title',
            [
                'label'       => __( 'Button Title', 'kodesk' ),
                'type'        => Controls_Manager::TEXT,
				'label_block' => true,
                'dynamic'     => [
                    'active' => true,
                ],
            ]
        );
        $this->add_control(
            'btn_link',
            [
                'label' => __( 'Button URL', 'kodesk' ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'kodesk' ),
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
            ]
        );
        $this->add_control(
            'query_number',
            [
                'label'   => esc_html__( 'Number of post', 'kodesk' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 9,
                'min'     => 1,
                'max'     => 100,
                'step'    => 1,
            ]
        );
        $this->add_control(
            'query_orderby',
            [
                'label'   => esc_html__( 'Order By', 'kodesk' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => array(
                    'date'       => esc_html__( 'Date', 'kodesk' ),
                    'title'      => esc_html__( 'Title', 'kodesk' ),
                    'menu_order' => esc_html__( 'Menu Order', 'kodesk' ),
                    'rand'       => esc_html__( 'Random', 'kodesk' ),
                ),
            ]
        );
        $this->add_control(
            'query_order',
            [
                'label'   => esc_html__( 'Order', 'kodesk' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'ASC',
                'options' => array(
                    'DESC' => esc_html__( 'DESC', 'kodesk' ),
                    'ASC'  => esc_html__( 'ASC', 'kodesk' ),
                ),
            ]
        );
        $this->add_control(
            'query_category',
            [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Category', 'kodesk'),
                'options' => get_categories_list('workspace_cat'),
            ]
        );
        $this->end_controls_section();
    }

    /**
     * Render button widget output on the frontend.
     * Written in PHP and used to generate the final HTML.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $allowed_tags = wp_kses_allowed_html('post');

        $paged = kodesk_set($_POST, 'paged') ? esc_attr($_POST['paged']) : 1;

        $this->add_render_attribute( 'wrapper', 'class', 'templatepath-kodesk' );
        $args = array(
            'post_type'      => 'workspace',
            'posts_per_page' => kodesk_set( $settings, 'query_number' ),
            'orderby'        => kodesk_set( $settings, 'query_orderby' ),
            'order'          => kodesk_set( $settings, 'query_order' ),
            'paged'          => $paged
        );

        if( kodesk_set( $settings, 'query_category' ) ) $args['workspace_cat'] = kodesk_set( $settings, 'query_category' );
        $query = new \WP_Query( $args );

        if ( $query->have_posts() ) { ?>
        
        <!-- workspaces-section -->
        <section class="workspaces-section sec-pad">
            <div class="auto-container">
                <div class="sec-title">
                    <h6><?php echo wp_kses( $settings['subtitle'], true ); ?></h6>
                    <h2><?php echo wp_kses( $settings['title'], true ); ?></h2>
                    
                    <?php if($settings['btn_link']['url'] and $settings['btn_title']) { ?>
                    <div class="btn-box">
                        <a href="<?php echo esc_url( $settings['btn_link']['url'] ); ?>" class="theme-btn btn-one"><span><?php echo wp_kses( $settings['btn_title'], true ); ?></span></a>
                    </div>
                    <?php } ?>
                </div>
                <div class="three-item-carousel owl-carousel owl-theme owl-nav-none dots-style-one">
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="workspaces-block-one">
                        <div class="inner-box">
                            <div class="image-box">
                                <span><?php echo wp_kses(get_post_meta( get_the_id(), 'feature_tag', true ), true); ?></span>
                                <figure class="image"><?php the_post_thumbnail('kodesk_370x470'); ?></figure>
                            </div>
                            <div class="content-box">
                                <div class="text"><i class="flaticon-pointer-inside-a-circle"></i> <?php echo wp_kses(get_post_meta( get_the_id(), 'address', true ), true); ?></div>
                                <h3><a href="<?php echo esc_url(get_post_meta( get_the_id(), 'ext_url', true )); ?>"><?php the_title(); ?></a></h3>
                                <div class="othre-options clearfix">
                                    <div class="pull-left"><?php echo wp_kses(get_post_meta( get_the_id(), 'price_package', true ), true); ?></div>
                                    <ul class="pull-right clearfix">
                                        <li><i class="flaticon-select"></i><?php echo wp_kses(get_post_meta( get_the_id(), 'square_feet', true ), true); ?></li>
                                        <li><i class="flaticon-user"></i><?php echo wp_kses(get_post_meta( get_the_id(), 'users', true ), true); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
        <!-- workspaces-section end -->
        
        <?php }

        wp_reset_postdata();
    }
}
