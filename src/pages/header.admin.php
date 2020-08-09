<?php

  if (!is_user_logged_in()) {
    wp_die();
  }

	// thickbox support
	add_thickbox();

?><div id="wll-important-notice" style="background-color:<?php echo $this->mcolor; ?>;">
  <span class="wll-notice-message">
    <!-- notes -->
  </span>
</div>
<header class="wll-header"><?php
  echo $this->menu_title();
  $this->tab_menu();
?></header>
	<div class="wrap"><h2></h2></div><!---admin notices -->
	<div class="wll-container">
	  <div class="wll-child">
	    	<div class="wll-grid-item">
	      	<div class="wll-padding">
						<p><!---innner paragraph -->
