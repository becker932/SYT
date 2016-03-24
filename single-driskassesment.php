<?php get_header(); ?>

<?php 
/** Themify Default Variables
 *  @var object */
global $themify;

?>

<?php 
if( have_posts() ) while ( have_posts() ) : the_post(); 
?>	
<!-- body -->
<div id="body" class="clearfix">
	<!-- layout -->
	<div id="layout" class="pagewidth clearfix">
        <?php themify_content_before(); //hook ?>
		<!-- content -->
		<div id="content" class="clearfix">
        	<?php themify_content_start(); //hook ?>

			<?php 

				get_template_part( 'includes/loop' , 'single');

				$meta = get_post_meta( get_the_ID());
				//echo "<h1>".$meta['syt-dra-title'][0]."</h1>";
				$str = "<table width='100%'>
							<tr>
							<th width='20%'>Assessor</th><td >".$meta['syt-dra-assessor'][0]."</td>
							<th width='20%'>Department</th><td>".$meta['syt-dra-department'][0]."</td>
							</tr>
							<tr>
							<th >Tasks being assessed</th><td  colspan='3'>".$meta['syt-dra-tasks-being-assessed'][0]."</td>
							</tr>
							<tr>
							<th>Date</th><td colspan='3'>".$meta['syt-dra-date'][0]."</td>
							</tr>
						</table>";
				echo $str;
			if( current_user_can( 'author' ) ){
				//<a href="#fancyboxID-1" class="fancybox">Click here to enter your details</a>
				echo '<div style="display:none" class="fancybox-hidden"><div id="fancyboxID-MainD">';
				echo do_shortcode ('[gravityform id="5" ajax="true" field_values="syt-ra-postid='.get_the_ID().'&syt-dra-title='.urlencode($meta['syt-dra-title'][0]).'&syt-dra-department='.urlencode($meta['syt-dra-department'][0]).'&syt-dra-assessor='.urlencode($meta['syt-dra-assessor'][0]).'&syt-dra-tasks-being-assessed='.urlencode($meta['syt-dra-tasks-being-assessed'][0]).'&syt-dra-date='.urlencode($meta['syt-dra-date'][0]).'"]');
				echo '</div></div>';
				echo '<div style="float:right;"> <a href="#fancyboxID-MainD" class="fancybox">edit these details <img width ="20" height="20" src="http://www.safetyyoutrust.com/wp-content/uploads/2014/10/Edit.png"></a></div>';

			} 
			?><br>

<?php 
echo do_shortcode('[expand targclass="maptastic" title="Advice on Task Assessment completion"]<ol>
<li>The Task Assessment forms should be completed by the competent person conducting the assessment in conjunction with relevant persons (e.g. staff carrying out the tasks) and passed to the HSA for filing on completion. The Assessments should be reviewed where operations, premises or personnel are changed and, in any case, on an annual basis.</li>
<li>In the first instance a first draft of the sheets should be completed by the Assessors as follows: -
<br>a. Familiarise themselves with the current HS&W Policies and Procedures
<br>b. For each Hazard identified in the Hazard Identification, identify the potential RISK arising from this hazard and those staff at risk and record in the "Persons at risk" field (write "everyone", if required).
<br>c. Review whether existing practice meets standard to be reached in the "Standards" field and record "yes" or "no" with the "Standards Met?" buttons.
<br>d. Record what documents / practices / discussions were involved in determining whether standards are being met in the "Standards" field.
<br>e.  If standards are not being met, record further action to be taken in the "Recommendations" field.</li>
<li>At a team meeting all staff should consider this draft and agree or revise the findings.</li>
<li>The team should agree by whom and when the action should be done. (This could be anyone in the team).</li>
<li> Once action has been taken, edit the task, set "Actioned?" to "Yes" and the enter the date of the action. <b>When you confirm the "Actioned" selection that you, the assessor, are confirming that the person record in the "Actioned By" field has implemented the action.</b></li>
</ol>[/expand]');
?><br>

<?php 
			// the query
			$args = array(
				'post_type'  => 'syt-dra-row',
				'meta_query' => array(
					array(
						'key'     => 'syt-dra-parent',
						'value'   =>  get_the_ID(),
					),
				),
			);

			$the_query = new WP_Query( $args ); ?>

			<?php if ( $the_query->have_posts() ) : ?>

				<!-- pagination here -->
				<table width ="100%">
					<tr>
						<th width = "35%">Hazard</th>
						<th width = "30%">Risk</th>
						<th>Standard<br>Met?</th>
						<th>Actioned</th>
						<th>View /<br> Edit</th>
					</tr>
				<!-- the loop -->
				<?php 

				while ( $the_query->have_posts() ) : $the_query->the_post(); 
					$meta =get_post_meta( get_the_ID());
					$uri = "syt-ra-postid=".urlencode(get_the_ID())."&syt-ra-hazard=".urlencode($meta['syt-ra-hazard'][0])."&syt-ra-actionedby=".urlencode($meta['syt-ra-actionedby'][0])."&syt-ra-risk=".urlencode($meta['syt-ra-risk'][0])."&syt-ra-atRisk=".urlencode($meta['syt-ra-atRisk'][0])."&syt-ra-currentControls=".urlencode($meta['syt-ra-currentControls'][0])."&syt-ra-standard=".urlencode($meta['syt-ra-standard'][0])."&syt-ra-recommendations=".urlencode($meta['syt-ra-recommendations'][0])."&syt-ra-standardMet=".urlencode($meta['syt-ra-standardMet'][0])."&syt-ra-actionSwitch=".urlencode($meta['syt-ra-actionSwitch'][0])."&syt-ra-actioned=".urlencode($meta['syt-ra-actioned'][0]);
					$str = "<tr>
								<td>".$meta['syt-ra-hazard'][0]." </td>
								<td>".$meta['syt-ra-risk'][0]."</td>
								<td align='center'>".$meta['syt-ra-standardMet'][0]."</td>
								<td align='center'>".$meta['syt-ra-actionSwitch'][0]."</td>
								<td align='center'>
									<a href='http://www.safetyyoutrust.com/?page_id=3662&".$uri."' class='fancybox-iframe'><img width ='20' height='20' src='http://www.safetyyoutrust.com/wp-content/uploads/2014/10/Edit.png'></a>
								</td>
								</tr>";	
					error_log($uri)	;
					echo $str;?>
				<?php endwhile; ?>
				</table>

				<!-- end of the loop -->
				
				<!-- pagination here -->

				<?php wp_reset_postdata(); ?>

			<?php else : ?>
				<p><?php echo '&nbsp;<br/>No tasks have been created for this risk assessment yet.' ; ?></p>
			<?php endif; ?>

			<?php 

			if( current_user_can( 'author' ) ){
				//<a href="#fancyboxID-1" class="fancybox">Click here to enter your details</a>
				echo '<div style="display:none" class="fancybox-hidden"><div id="fancyboxID-1">';
				echo do_shortcode ('[gravityform id="3" ajax="true" field_values="syt-dra-parent='.get_the_ID().'"]');
				echo '</div></div>';
				echo '<br/><div style="float:right;align:center;text-align:center;"> <a href="#fancyboxID-1" class="fancybox"> <img src="http://www.safetyyoutrust.com/wp-content/uploads/2014/10/Add.png"><br>Add Task</a></div>';

			} 
			?>
			<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			<!-- /comments -->
			
            <?php themify_content_end(); //hook ?>
		</div>
		<!--/content -->
        <?php themify_content_after() //hook; ?>

<?php endwhile; ?>

<?php 
/////////////////////////////////////////////
// Sidebar							
/////////////////////////////////////////////
if ($themify->layout != "sidebar-none"): get_sidebar(); endif; ?>

	</div>
	<!--/layout --> 
	
</div>
<!--/body -->

<?php get_footer(); ?>