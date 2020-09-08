<?php

/**
 * Template Name: Add Proposition

 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 */
$terms = get_terms([
    'taxonomy' => 'thematique',
    'hide_empty' => false,
]);

/* L'envoi du formulaire d'ajout  */

if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == "prop") {
    
        $title = $_POST['title'];
        $post_type = 'proposition';
    
        if(!function_exists('wp_get_current_user')) {
            include(ABSPATH . "wp-includes/pluggable.php"); 
        }
        $current_user = wp_get_current_user();
        
        /* L'ajout du CPT proposition */
        $term = intval($_POST["thematique"]);
        $front_post = array(
        'post_title'    => $title,
        'post_status'   => 'publish',          
        'post_type'     => $post_type,
        'post_content' => $_POST["description"],
        'post_author' => $current_user->ID,
        'comment_status' => 'open',
        );
        
        $post_id = wp_insert_post($front_post);

        /* Upload de l'image du proposition */

        $upload = wp_upload_bits($_FILES["imagep"]["name"], null, file_get_contents($_FILES["imagep"]["tmp_name"]));

        $filename = $upload['file'];
        $uploadfile = wp_upload_dir()["path"];

        move_uploaded_file($filename, $uploadfile);  

            $wp_filetype = wp_check_filetype($filename, null );
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment( $attachment, $filename, $posts );
             
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

            wp_update_attachment_metadata( $attach_id, $attach_data );
            set_post_thumbnail( $post_id, $attach_id );  

        
        /* Assigner la ou les thématiques à la proposition déjà creéé */
        $taxonomy = 'thematique';
        $themes = array();
    
        foreach ($terms as $term) {
            if($_POST[$term->term_id]){
                $themes[] = $_POST[$term->term_id];
            }
        }
  
        wp_set_object_terms($post_id, $themes, $taxonomy);
   
        wp_redirect(get_permalink($post_id));
        exit();
    }


get_header();
?>

<!-- Vue de la page Ajouter une proposition -->
<div class="container">
    <div class="row" style="margin-top: 20px;margin-bottom: 20px;">
        <div class="col-lg-2"></div>
        <div class="col-lg-8" style="margin-top: 30px;">
            <h2 style="text-align:center">Faire une proposition</h2>
            <p>
            Comment s'émaniciper à nouveau des écrans pour mieux les maîtriser ? Faut-il promouvoir des comportements, du bon sens, des techniques ?
            Toutes les idées sont importantes, à vous d'écrire la vôtre.
            </p>
            <form method='POST' enctype="multipart/form-data">
                <div class="form-group">
                    <label>Titre *</label>
                    <input type='text' value='' class="form-control" name='title' placeholder="Exemple : Placer des brouilleurs dans les écoles" required>
                </div>
                <div class="form-group">
                    <label>Description *</label>
                    <textarea class='form-control' name='description' placeholder="Exemple : Mon fils de 14 ans est constamment" required></textarea>
                </div>
                <div class="form-group">
                    <label>Thématique :</label><br>

                    <!-- Liste des thématiques -->

                    <?php foreach ($terms as $term) { ?>

                        
                        <input type="checkbox" id="<?php echo $term->name; ?>" name="<?php echo $term->term_id; ?>" value="<?php echo $term->slug; ?>">
                        <label><?php echo $term->name; ?></label><br>
                    
                    <?php } ?>
   
                </div>

                <div class="form-group">
                <label>Ajouter une photo :</label>
                    <input type='file' name='imagep' id='imagep' class='form-control'>
                </div>

                <div class="form-group">
                    <button class='btn btn-primary'>Continuer</button>
                </div>
                </div>
                <input type='hidden' name='action' value='prop' />
            </form>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>

<?php
  
 get_footer();