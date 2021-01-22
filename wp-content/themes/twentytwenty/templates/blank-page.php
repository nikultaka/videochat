<?php /* Template Name: Blank Page */ 
global $current_user;
global $post;
wp_get_current_user();
$currentUser = wp_get_current_user();
$post_slug = $post->post_name;
?>   
<html>


<head>



<style type="text/css">
	html {
		margin-top: 0px !important;
	}
	/*===navbar css start===*/
	.navbar{
	  list-style: none;
	  display: flex;
	  justify-content: space-between;
	  height: 60px;
	  align-items: center;
	  background: #1c1919;
	  max-width: 100%;
	  width: 100%;
	}
	.link{
	  text-decoration: none !important;
	  color: #fff;
	  font-weight: 600;
	}
	.link:hover{
	  color: #ad4033;
	}
	.post-edit {
	  display: none;
	}
	/*.section-inner {
	  display: none; 
	}*/
	/*=navabr css end=*/

	#user-registration {
		margin: auto !important;
	}
	.user-registration {
		background: white !important;
	}
</style>



<style type="text/css">
	.loader{
	  position: fixed;
	  left: 0px;
	  top: 0px;
	  width: 100%;
	  height: 100%;
	  z-index: 9999;
	  background: url('//upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Phi_fenomeni.gif/50px-Phi_fenomeni.gif') 
	              50% 50% no-repeat rgb(249,249,249);
	}
</style>
 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>  
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('website-custom-plugin/WCP/assets/css/style.css'); ?>" >    
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />



<?php 
if($post_slug!='video-chat') {
	wp_head();	
}
?>
	

</head>




<body>

	<?php if($post_slug!='video-chat') { ?>
		<div class="loader" style="display: none;"></div>


		<div class="game_wrapper">
				<div class="navbar_conainer">
				    <ul class="navbar">
				      <li><a href="<?php echo site_url(); ?>" class="link">Home</a></li>
				      	<?php if (!is_user_logged_in()) { ?>	
				      		<li><a href="<?php echo site_url('index.php/registration'); ?>" class="link">Signup</a></li>
				      		<li><a href="<?php echo site_url('index.php/my-account'); ?>" class="link">Login</a></li>
					  		<?php } else { ?>
					  			<li><a href="<?php echo site_url('index.php/my-account'); ?>" class="link">Welcome (<?php echo $current_user->display_name; ?>)</a></li>
					  			<li><a href="<?php echo site_url('index.php/join-room'); ?>" class="link">Join Game</a></li>
					  			<li><a href="<?php echo site_url('index.php/create-room'); ?>" class="link">New Game</a></li>
					  			<li><a href="<?php echo wp_logout_url(); ?>" class="link">Logout</a></li>
					  		<?php } ?>	
				    </ul>
				</div>
				<?php
				// Start the loop.
				while ( have_posts() ) : the_post();
				    get_template_part( 'template-parts/content', 'page' );
				endwhile;
				?>
		</div>
	<?php } else {
		while ( have_posts() ) : the_post();
		    get_template_part( 'template-parts/content', 'page' );
		endwhile;
	} ?>	

	<?php 
	wp_footer();
	?>
</body>
</html>