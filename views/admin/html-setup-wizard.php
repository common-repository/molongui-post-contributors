<?php

use Molongui\Contributors\Common\Modules\Settings;
use Molongui\Contributors\Integrations;
use Molongui\Contributors\Wizard;

defined( 'ABSPATH' ) or exit; // Exit if accessed directly

$options      = Settings::get();
$integration  = Integrations::current_theme() !== 'none';
$wizard_steps = Wizard::get_step_count();
$wizard_step  = 0;
?>

<!-- Step 0: Welcome -->
<div id="step-<?php echo esc_attr( $wizard_step ); ?>" class="molongui-setup-wizard__step active">

    <div class="molongui-setup-wizard__title">

        <div class="molongui-setup-wizard__track">
            <span><?php esc_html_e( "Welcome to the Setup Wizard!", 'molongui-post-contributors' ); ?></span>
        </div>

        <h2><?php esc_html_e( "Easily Manage and Display Post Contributors", 'molongui-post-contributors' ); ?></h2>

        <div class="molongui-setup-wizard__subtitle"></div>

    </div>

    <div class="molongui-setup-wizard__description">

        <div class="molongui-setup-wizard__column">
            <p><?php esc_html_e( "This quick setup guide will help you understand how the main features of the plugin work and assist you in configuring some essential settings.", 'molongui-post-contributors' ); ?></p>
            <p><?php esc_html_e( "You can customize the plugin further in the plugin settings page at any time.", 'molongui-post-contributors' ); ?></p>
            <p><?php esc_html_e( "Click \"Get Started\" to begin the setup and start enhancing your post bylines with contributor information.", 'molongui-post-contributors' ); ?></p>
        </div>

        <div class="molongui-setup-wizard__column">

        </div>

    </div>

    <div class="molongui-setup-wizard__nav">

        <button type="button" class="molongui-setup-wizard__button next primary">
            <?php esc_html_e( "Get Started", 'molongui-post-contributors' ); ?>
        </button>

    </div>

</div><!-- step 0 -->

<!-- Step 1: How to add a contributor to a post -->
<?php $wizard_step++; ?>
<div id="step-<?php echo esc_attr( $wizard_step ); ?>" class="molongui-setup-wizard__step">

    <div class="molongui-setup-wizard__title">

        <div class="molongui-setup-wizard__track">
            <span>
                <?php
                /*! // translators: %1$s: Current wizard step. %2$s: Total wizard steps. */
                printf( esc_html_x( "Step %1\$s of %2\$s", 'Current step versus total number of wizard steps', 'molongui-post-contributors' ), $wizard_step, $wizard_steps );
                ?>
            </span>
        </div>

        <h2><?php esc_html_e( "How to add a contributor to a post", 'molongui-post-contributors' ); ?></h2>

        <div class="molongui-setup-wizard__subtitle"></div>
    </div>

    <div class="molongui-setup-wizard__description">

        <div class="molongui-setup-wizard__column">
            <p><?php esc_html_e( "It's as easy as following these steps:", 'molongui-post-contributors' ); ?></p>
            <ol>
                <li><?php esc_html_e( "Edit a post", 'molongui-post-contributors' ); ?></li>
                <li><?php echo wp_kses_post( sprintf( __( "Locate the %sContributors%s panel", 'molongui-post-contributors' ), '<strong>', '</strong>' ) ); ?></li>
                <li><?php esc_html_e( "Type the name to add", 'molongui-post-contributors' ); ?></li>
                <li><?php esc_html_e( "Make your selection", 'molongui-post-contributors' ); ?></li>
                <li><?php esc_html_e( "Choose how they contributed", 'molongui-post-contributors' ); ?></li>
                <li><?php esc_html_e( "Save your changes", 'molongui-post-contributors' ); ?></li>
            </ol>
            <p><?php esc_html_e( "That's it!", 'molongui-post-contributors' ); ?></p>
        </div>

        <div class="molongui-setup-wizard__column">
            <img src="<?php echo esc_url( MOLONGUI_CONTRIBUTORS_URL . '/assets/img/wizard/contributor_selector.png' ); ?>" alt="<?php esc_attr_e( "Contributors Selector Screenshot", 'molongui-post-contributors' ); ?>">
        </div>

    </div>

    <div class="molongui-setup-wizard__nav">

        <button type="button" class="molongui-setup-wizard__button back">
            <?php esc_html_e( "Back", 'molongui-post-contributors' ); ?>
        </button>

        <button type="button" class="molongui-setup-wizard__button next primary">
            <?php esc_html_e( "Next", 'molongui-post-contributors' ); ?>
        </button>

    </div>

</div><!-- step 1 -->

<!-- Step 2: How to add contributor roles -->
<?php $wizard_step++; ?>
<div id="step-<?php echo esc_attr( $wizard_step ); ?>" class="molongui-setup-wizard__step">

    <div class="molongui-setup-wizard__title">

        <div class="molongui-setup-wizard__track">
            <span>
                <?php
                /*! // translators: %1$s: Current wizard step. %2$s: Total wizard steps. */
                printf( esc_html_x( "Step %1\$s of %2\$s", 'Current step versus total number of wizard steps', 'molongui-post-contributors' ), $wizard_step, $wizard_steps );
                ?>
            </span>
        </div>

        <h2><?php esc_html_e( "How to Add Custom Contributor Roles", 'molongui-post-contributors' ); ?></h2>

        <div class="molongui-setup-wizard__subtitle"></div>
    </div>

    <div class="molongui-setup-wizard__description">

        <div class="molongui-setup-wizard__column">
            <p>
                <?php
                /*! // translators: %1$s: <code>. %2$s: </code>. */
                echo wp_kses_post( sprintf( __( "You can easily manage your contributor roles at %1\$sSettings > Post Contributors > Contributors > Contributor Roles%2\$s.", 'molongui-post-contributors' ), '<code>', '</code>' ) );
                ?>
            </p>
            <p>
                <?php
                /*! // translators: %1$s: <code>. %2$s: </code>. */
                echo wp_kses_post( sprintf( __( "For your convenience, we will add some default roles (%1\$sReviewer, Fact-checker, Illustrator, and Photographer%2\$s) unless you toggle off the switch below:", 'molongui-post-contributors' ), '<strong>', '</strong>' ) );
                ?>
            </p>
            <div class="toggle">
                <div class="button r" id="button-1">
                    <input type="checkbox" class="checkbox" name="default_contributor_roles" checked>
                    <div class="knobs"></div>
                    <div class="layer"></div>
                </div>
                <div class="toggle__label">
                    <label>
                        <?php esc_html_e( "Add default contributor roles?", 'molongui-post-contributors' ); ?>
                    </label>
                </div>
            </div>
        </div>

        <div class="molongui-setup-wizard__column">

        </div>

    </div>

    <div class="molongui-setup-wizard__nav">

        <button type="button" class="molongui-setup-wizard__button back">
            <?php esc_html_e( "Back", 'molongui-post-contributors' ); ?>
        </button>

        <button type="button" class="molongui-setup-wizard__button next primary">
            <?php esc_html_e( "Next", 'molongui-post-contributors' ); ?>
        </button>

    </div>

</div><!-- step 2 -->

<!-- Step 3: How to display contributor names in your post-->
<?php $wizard_step++; ?>
<div id="step-<?php echo esc_attr( $wizard_step ); ?>" class="molongui-setup-wizard__step">

    <div class="molongui-setup-wizard__title">

        <div class="molongui-setup-wizard__track">
            <span>
                <?php
                /*! // translators: %1$s: Current wizard step. %2$s: Total wizard steps. */
                printf( esc_html_x( "Step %1\$s of %2\$s", 'Current step versus total number of wizard steps', 'molongui-post-contributors' ), $wizard_step, $wizard_steps );
                ?>
            </span>
        </div>

        <h2><?php esc_html_e( "How to Display Contributors on Your Posts", 'molongui-post-contributors' ); ?></h2>

        <div class="molongui-setup-wizard__subtitle"></div>
    </div>

    <div class="molongui-setup-wizard__description">

        <div class="molongui-setup-wizard__column">

            <?php if ( $integration ) : ?>

            <p>
                <?php
                /*! // translators: %1$s: <strong>. %2$s: </strong>. */
                echo wp_kses_post( sprintf( __( "Actually, %syou need to do nothing at all%s! Yay!", 'molongui-post-contributors' ), '<strong>', '</strong>' ) );
                ?>
                <span style="font-size:30px">🎉</span>
            </p>
            <p><?php esc_html_e( "The contributor name will be displayed below your post byline, where the post author name is shown.", 'molongui-post-contributors' ); ?></p>
            <p>
                <?php
                /*! // translators: %1$s: <code>. %2$s: </code>. */
                echo wp_kses_post( sprintf( __( "However, if you use a page builder to customize your post layouts or need more customization options, head to the plugin settings page at %sSettings > Post Contributors%s", 'molongui-post-contributors' ), '<code>', '</code>' ));
                ?>
            </p>
            <input type="hidden" name="contributors_display" value="other">

            <?php else : ?>

                <p><?php esc_html_e( "The plugin provides several options for you to display the contributor name on your posts. Pick the one that you prefer:", 'molongui-post-contributors' ); ?></p>
                <div class="molongui-input-radios-with-icons">
                <?php
                $label       = __( "Automatically, at the top of the post content", 'molongui-post-contributors' );
                $description = __( "A new post byline is added to the top of your post content, typically appearing just below the featured image and title. You can configure which post information to display. <pre>Contact us if you need help hiding your existing byline. We will help!</pre>", 'molongui-post-contributors' );
                $this->render_radio( 'contributors_display', $label, $description, 'add_to_content', !$integration );

                $label       = __( "Automatically, using a new template for your posts", 'molongui-post-contributors' );
                $description = __( "Use one of the ready-to-use templates packaged with the plugin to ensure full compatibility and avoid any issues. You only need to pick the layout that you like most, nothing else. <pre>This will make your posts look different from how they currently appear</pre>", 'molongui-post-contributors' );
                $this->render_radio( 'contributors_display', $label, $description, 'template_override' );

                $label       = __( "Manually, using the provided shortcode or widget", 'molongui-post-contributors' );
                $description = __( "If you design the layout for your posts by either coding the file template or using a theme builder, this is likely the option you want. <pre>Elementor widget and Gutenberg (WP Block editor) block available</pre>", 'molongui-post-contributors' );
                $this->render_radio( 'contributors_display', $label, $description, 'shortcode' );
                ?>
            </div>

            <?php endif; ?>

        </div>

        <div class="molongui-setup-wizard__column">

        </div>

    </div>

    <div class="molongui-setup-wizard__nav">

        <button type="button" class="molongui-setup-wizard__button back">
            <?php esc_html_e( "Back", 'molongui-post-contributors' ); ?>
        </button>

        <button type="button" class="molongui-setup-wizard__button next primary">
            <?php esc_html_e( "Next", 'molongui-post-contributors' ); ?>
        </button>

    </div>

</div><!-- step 3 -->

<?php if ( !$integration ) : $wizard_step++; ?>
<!-- Step 4: Which info to display in the post byline -->
<div id="step-<?php echo esc_attr( $wizard_step ); ?>" class="molongui-setup-wizard__step">

        <div class="molongui-setup-wizard__title">

            <div class="molongui-setup-wizard__track">
            <span>
                <?php
                /*! // translators: %1$s: Current wizard step. %2$s: Total wizard steps. */
                printf( esc_html_x( "Step %1\$s of %2\$s", 'Current step versus total number of wizard steps', 'molongui-post-contributors' ), $wizard_step, $wizard_steps );
                ?>
            </span>
            </div>

            <h2><?php esc_html_e( "Which information should be displayed on the byline?", 'molongui-post-contributors' ); ?></h2>

            <div class="molongui-setup-wizard__subtitle"></div>
        </div>

        <div class="molongui-setup-wizard__description">

            <div class="molongui-setup-wizard__column">
                <p><?php esc_html_e( "A byline is a line that tells readers who has written a post. At the very least, it comprises the author's name, but a great byline can contain much more than that.", 'molongui-post-contributors' ); ?></p>

                <div class="molongui-plugin-features-list">
                    <?php
                    $id          = 'show_author';
                    $label       = __( "Author Name", 'molongui-post-contributors' );
                    $description = __( "Ensure your authors get credit for the content they write.", 'molongui-post-contributors' );
                    $this->render_long_checkbox( $id, $label, $description, !empty( $options[$id] ) );

                    $id          = 'show_author_avatar';
                    $label       = __( "Author Avatar", 'molongui-post-contributors' );
                    $description = __( "People are immediately more likely to trust a face they can see than just a name.", 'molongui-post-contributors' );
                    $this->render_long_checkbox( $id, $label, $description, !empty( $options[$id] ), true, true );

                    $id          = 'show_contributors';
                    $label       = __( "Contributor Name", 'molongui-post-contributors' );
                    $description = __( "Contributors are photographers, producers, reviewers, fact-checkers, and other bylines that can be assigned to the pieces of content on which they have worked.", 'molongui-post-contributors' );
                    $this->render_long_checkbox( $id, $label, $description, !empty( $options[$id] ) );

                    $id          = 'show_contributors_avatar';
                    $label       = __( "Contributor Avatar", 'molongui-post-contributors' );
                    $description = __( "Because pictures are more likely to be remembered than words.", 'molongui-post-contributors' );
                    $this->render_long_checkbox( $id, $label, $description, !empty( $options[$id] ), true, true );

                    $id          = 'show_publish_date';
                    $label       = __( "Publish Date", 'molongui-post-contributors' );
                    $description = __( "It is good practice to include a date tag at the top of a post, as it helps the reader understand the context and also builds trust.", 'molongui-post-contributors' );
                    $this->render_long_checkbox( $id, $label, $description, !empty( $options[$id] ) );

                    $id          = 'show_update_date';
                    $label       = __( "Update Date", 'molongui-post-contributors' );
                    $description = __( "A 'Last modified' date is going to be more useful as a reflection of how recent the information in the post is.", 'molongui-post-contributors' );
                    $this->render_long_checkbox( $id, $label, $description, !empty( $options[$id] ) );

                    $id          = 'show_categories';
                    $label       = __( "Categories", 'molongui-post-contributors' );
                    $description = __( "Displaying post categories not only improves the user experience but also strengthens your site's SEO.", 'molongui-post-contributors' );
                    $this->render_long_checkbox( $id, $label, $description, !empty( $options[$id] ) );

                    $id          = 'show_tags';
                    $label       = __( "Tags", 'molongui-post-contributors' );
                    $description = __( "Enhance the user experience, improve content discoverability and support SEO efforts.", 'molongui-post-contributors' );
                    $this->render_long_checkbox( 'show_tags', $label, $description, !empty( $options[$id] ) );

                    $id          = 'show_comment_link';
                    $label       = __( "Comments count", 'molongui-post-contributors' );
                    $description = __( "Enhance user engagement, provide social proof, contribute to SEO, and offer valuable insights into your audience's preferences.", 'molongui-post-contributors' );
                    $this->render_long_checkbox( $id, $label, $description, !empty( $options[$id] ) );
                    ?>
                </div>
            </div>

            <div class="molongui-setup-wizard__column">

            </div>

        </div>

        <div class="molongui-setup-wizard__nav">

            <button type="button" class="molongui-setup-wizard__button back">
                <?php esc_html_e( "Back", 'molongui-post-contributors' ); ?>
            </button>

            <button type="button" class="molongui-setup-wizard__button next primary">
                <?php esc_html_e( "Next", 'molongui-post-contributors' ); ?>
            </button>

        </div>

    </div><!-- step 4 -->
<?php endif; ?>

<!-- Step 5/4: Go Pro -->
<?php $wizard_step++; ?>
<div id="step-<?php echo esc_attr( $wizard_step ); ?>" class="molongui-setup-wizard__step">

    <div class="molongui-setup-wizard__title">

        <div class="molongui-setup-wizard__track">
            <span>
                <?php esc_html_e( "Need more features?", 'molongui-post-contributors' ); ?>
            </span>
        </div>

        <h2><?php esc_html_e( "Need to Add Multiple Contributors to a Post?", 'molongui-post-contributors' ); ?></h2>

        <div class="molongui-setup-wizard__subtitle"></div>
    </div>

    <div class="molongui-setup-wizard__description">

        <div class="molongui-setup-wizard__column">
            <p><?php esc_html_e( "Upgrade to Pro to unlock additional features:", 'molongui-post-contributors' ); ?></p>
            <ul class="premium-features">
                <li><?php echo wp_kses_post( sprintf( __( "%sAdd Multiple Contributors%s. Easily add and manage multiple contributors for each post.", 'molongui-post-contributors' ), '<strong>', '</strong>' ) ); ?></li>
                <li><?php echo wp_kses_post( sprintf( __( "%sDisplay Contributor Avatars%s. Enhance your post bylines with contributor avatars for a more engaging display.", 'molongui-post-contributors' ), '<strong>', '</strong>' ) ); ?></li>
                <li><?php esc_html_e( "Assign a contributor multiple times to the same post with different roles", 'molongui-post-contributors' ); ?></li>
                <li><?php echo wp_kses_post( sprintf( __( "%sGuest contributors%s. Add contributor names without them having a user account in your site. ", 'molongui-post-contributors' ), '<strong>', '</strong>' ) ); ?></li>
                <li><?php esc_html_e( "And Many More!", 'molongui-post-contributors' ); ?></li>
            </ul>
            <p>
                <?php
                /*! // translators: %1$s: <a> tag. %2$s: </a> tag. */
                echo wp_kses_post( sprintf( __( "Click %1\$shere%2\$s to learn more and upgrade today!", 'molongui-post-contributors' ), '<a href="'.esc_url( MOLONGUI_CONTRIBUTORS_WEB ).'" target="_blank" class="upgrade">', '</a>' ) );
                ?>
            </p>
        </div>

        <div class="molongui-setup-wizard__column">

        </div>

    </div>

    <div class="molongui-setup-wizard__nav">

        <button type="button" class="molongui-setup-wizard__button back">
            <?php esc_html_e( "Back", 'molongui-post-contributors' ); ?>
        </button>

        <button type="button" class="molongui-setup-wizard__button next primary">
            <?php esc_html_e( "Next", 'molongui-post-contributors' ); ?>
        </button>

    </div>

</div><!-- step 5/4 -->

<!-- Finish -->
<?php $wizard_step++; ?>
<div id="step-<?php echo esc_attr( $wizard_step ); ?>" class="molongui-setup-wizard__step">

                <div class="molongui-setup-wizard__title">

                    <div class="molongui-setup-wizard__track">
                        <span>
                            <?php esc_html_e( "Setup Complete!", 'molongui-post-contributors' ); ?>
                        </span>
                    </div>

                    <h2><?php esc_html_e( "You’re All Set!", 'molongui-post-contributors' ); ?></h2>

                    <div class="molongui-setup-wizard__subtitle"></div>
                </div>

                <div class="molongui-setup-wizard__description">

                    <div class="molongui-setup-wizard__column">
                        <p><?php esc_html_e( "Here are a few next steps to help you get the most out of the plugin:", 'molongui-post-contributors' ); ?></p>
                        <ul>
                            <li><?php echo wp_kses_post( sprintf( __( "%sAdd a Contributor to a Post%s. Edit any post to add a contributor and see how it is beautifully displayed in the post byline on the frontend.", 'molongui-post-contributors' ), '<strong>', '</strong>' ) ); ?></li>
                            <li><?php echo wp_kses_post( sprintf( __( "%sAdd New Contributor Roles%s. Customize your contributor roles to better manage and display different types of contributors", 'molongui-post-contributors' ), '<strong>', '</strong>' ) ); ?></li>
                            <li><?php echo wp_kses_post( sprintf( __( "%sGo to the Plugin Settings Page%s. Head to the plugin settings page to explore more customization options and fine-tune your setup", 'molongui-post-contributors' ), '<strong>', '</strong>' ) ); ?></li>
                        </ul>
                        <p><?php esc_html_e( "Click \"Finish\" to complete the setup and start using the plugin.", 'molongui-post-contributors' ); ?></p>
                    </div>

                    <div class="molongui-setup-wizard__column">

                    </div>

                </div>

                <div class="molongui-setup-wizard__nav">

                    <button type="button" class="molongui-setup-wizard__button back">
                        <?php esc_html_e( "Back", 'molongui-post-contributors' ); ?>
                    </button>

                    <button type="button" class="molongui-setup-wizard__button finish primary">
                        <?php esc_html_e( "Finish!", 'molongui-post-contributors' ); ?>
                    </button>

                </div>

            </div><!-- finish -->