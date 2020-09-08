<?php

/**
 * Template Name: Propositions

 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 */


get_header();
?>
<div class="container">
    <div class="row">
      <div class="col-lg-2"></div>
      <div class="col-lg-4" style="margin-top: 40px; padding-left:0px !important">
      <h2>Liste des propostitions</h2>
      </div>
      <div class="col-lg-4" style="text-align:right;margin-top: 40px; padding-right:0px !important">
      
      <a href="/ajouter-une-proposition" style="padding: 10px;background-color: #ed926c;color: white;">Ajouter une proposition</a>
      </div>
      <div class="col-lg-2"></div>
    </div>

<?php

/* Boucler les CPT proposition */
$args = array(
    'post_type'   => 'proposition',
    
   );
$loop = new WP_Query($args);

if( $loop->have_posts() ):
    ?>
 
    <?php
      while( $loop->have_posts() ) :
        $loop->the_post();
        
        /* Les thématiques de chaque proposition */
        $post_terms = wp_get_post_terms( get_the_id(), 'thematique', $args = array() );

        /* Les commentaires de chaque proposition */
        $comments = get_comments( array( 'post_id' => get_the_id() ) );
    ?>

  <div class="row">

    <div class="col-lg-2"></div>

    <div class="col-lg-8" id="propsition">

          <p id="thematique">
            <?php 
            foreach ($post_terms as $post_term) {
                echo $post_term->name.", ";
            }
            ?>
          </p>

          <p id="author"><?php if(get_the_author() != "") { echo get_the_author(); } else { echo "Internaute"; } ?></p>

          <p id="title"><a href="<?php echo get_the_permalink(); ?>"><b><?php echo get_the_title()?></b></a></p>

          <p id="description"><?php echo get_the_content()?></p>

          <p id="image"><?php echo get_the_post_thumbnail()?></p>
          
          <?php if(is_user_logged_in()){ ?>

          <p id="like">
            <?php $nonce = wp_create_nonce( 'pt_like_it_nonce' );
              $link_likes = admin_url('admin-ajax.php?action=like_post&post_id='.get_the_id().'&nonce='.$nonce);
              $link_dislikes = admin_url('admin-ajax.php?action=dislike_post&post_id='.get_the_id().'&nonce='.$nonce);
              $likes = get_post_meta( get_the_id(), '_pt_likes', true );
              $likes = ( empty( $likes ) ) ? 0 : $likes;
              $dislikes = get_post_meta( get_the_id(), '_pt_dislikes', true );
              $dislikes = ( empty( $dislikes ) ) ? 0 : $dislikes; ?>

                          <a class="like-button" href="<?php echo $link_likes; ?>" data-id="<?php echo get_the_id(); ?>" 
                          data-nonce="<?php echo $nonce; ?>"> 
                          <i class="fa fa-thumbs-up" style="color:#f4511e;font-size: 30px;"></i>
                          </a>
                          <span id="like-count-<?php echo get_the_id(); ?>" class="like-count" style="margin-right: 10px;font-size: 30px;">
                          <?php echo $likes; ?></span>

                          <a class="dislike-button" href="<?php echo $link_dislikes; ?>" data-id="<?php echo get_the_id(); ?>" 
                          data-nonce="<?php echo $nonce; ?>">
                          <i class="fa fa-thumbs-down" style="color:grey;font-size: 30px;"></i>
                          </a>
                          <span id="dislike-count-<?php echo get_the_id(); ?>" class="dislike-count" style="font-size: 30px;"><?php echo $dislikes; ?></span>
                  
            </p>

        <?php } 

          if($comments){
            ?>
            <p id="comments" style="margin-bottom: 0px !important;"><b>Commentaires :</b></p>
            <p id="comments">
            <?php foreach ( $comments as $comment ) :
                    echo "<b>".$comment->comment_author . '</b><br />' . $comment->comment_content;
                  endforeach;
          }

          if(is_user_logged_in()){
          comments_template(); 
          }
        ?>
          
          </p>
          </div>
          <div class="col-lg-2"></div>
    </div>
        <?php
      endwhile;
      wp_reset_postdata();
    ?>
  
<?php
else :
?>

<div class="row">
  <div class="col-lg-2"></div>
  <div class="col-lg-8" style="padding-left: 0px;padding-top: 50px;padding-bottom: 50px;">
    <?php esc_html_e( 'Aucune proposition trouvée', 'text-domain' ); ?>
  </div>
  <div class="col-lg-2"></div>
</div>
</div>
  <?php
endif;

get_footer();