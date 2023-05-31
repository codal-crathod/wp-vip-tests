<?php
/**
 * Job List
 *
 * @package HelloElementorChild
 */

 function open_jobs(){
	ob_start();
	$openings = [
		'Glendale, CA' => [
			'Engineering' => [],
			'Product' => [],
			'Customer Success' => [],
			'Sales' => [],
			'Marketing' => [],
			'Strategy & Operations' => [],
			'Talent Acquisition' => [],
			'Data Solutions' => [],
			'Member Support' => [],
			'People Operations' => [],
			'Finance & Accounting' => [],
			'Legal' => [],
			'Other' => [],
		],
		'Yerevan' => [
			'Engineering' => [],
			'Product' => [],
			'Customer Success' => [],
			'Sales' => [],
			'Marketing' => [],
			'Strategy & Operations' => [],
			'Talent Acquisition' => [],
			'Data Solutions' => [],
			'Member Support' => [],
			'People Operations' => [],
			'Finance & Accounting' => [],
			'Legal' => [],
			'Other' => [],
		],
		'Remote' => [
			'Engineering' => [],
			'Product' => [],
			'Customer Success' => [],
			'Sales' => [],
			'Marketing' => [],
			'Strategy & Operations' => [],
			'Talent Acquisition' => [],
			'Data Solutions' => [],
			'Member Support' => [],
			'People Operations' => [],
			'Finance & Accounting' => [],
			'Legal' => [],
			'Other' => [],
		],
	];
	$openings_data = json_decode(file_get_contents("https://api.lever.co/v0/postings/disqo?mode=json"));
	
	foreach ($openings_data as $opening) {
		$openings[$opening->categories->location][$opening->categories->department][] = $opening;
	}
	?>
	<div class="careers-typography">
		<div class="wrapper" id="top">
			<div class="page-content">
				<section class="component-join-the-team-section"
						 data-action="impression" data-category="careers" data-label="careers-secondary-menu"
						 data-value="careers_open-roles-section">
					<div class="m-section-inner">
						<div class="m-section-sidebar">
							<h2 class="sec_title">Search keywords</h2>
							<div class="key_search_div">
								<input name="key_search" id="job_key_search" value="" placeholder="Keywords">
							</div>
							<h2 class="sec_title">Locations</h2>
							<ul class="component-vertical-tabs-nav js-tabs-nav">
								<?php $i = 0;
								foreach ($openings as $location => $departments): $i++; ?>
									<li>
										<button data-tab="#tab-<?= $i; ?>"
												class="tab-btn <?= $i == 1 ? 'opened-on-load' : null; ?>"><?= $location === 'Yerevan' ? 'Yerevan, Armenia' : $location; ?></button>
									</li>
								<?php endforeach; ?>
								<li>
										<button data-tab="#tab-all"
												class="tab-btn full-list">All locations</button>
									</li>
							</ul>
						</div>
						<div class="m-section-main">
							
							<div class="tabs-container">
							
								<?php $i = 0;
								foreach ($openings as $location => $departments): $i++; ?>
									<div class="tab <?= $i == 1 ? 'tab-active' : null; ?>" id="tab-<?= $i; ?>"
										 data-action="impression" data-category="careers"
										 data-label="careers-open-roles" data-value="<?= $location; ?>">
										 <div class="location-fixed">location</div>
										<h3 class="tab-location-header">
											<?= $location; ?>
										</h3>
										<div class="accordion" id="locationsAccordion">
											<?php $n = 0;
											foreach ($departments as $department => $jobs): $n++; ?>
												<?php if (!empty($jobs)): ?>
													<div class="location-card"
														 data-action="click" data-category="careers" data-label="role"
														 data-value="<?= $department; ?>">
														<div id="heading-<?= $i; ?>-<?= $n; ?>" class="acc-title" tabindex="0">
															<h4 class="mb-0">
																<div class="collapse-title collapsed"
																	 data-toggle="collapse"
																	 data-target="#collapse-<?= $i; ?>-<?= $n; ?>"
																	 aria-expanded="false"
																	 aria-controls="collapse-<?= $i; ?>-<?= $n; ?>">
																	<?= $department; ?>
																</div>
															</h4>
														</div>
														<div id="collapse-<?= $i; ?>-<?= $n; ?>" class="collapse"
															 aria-labelledby="heading-<?= $i; ?>-<?= $n; ?>"
															 data-parent="#locationsAccordion">
															<ul class="location-card-content">
																<?php foreach ($jobs as $job): ?>
																	<li class="location-card-content-links">
																		<a target="_blank"
																		   href="<?= $job->hostedUrl; ?>"><?= $job->text; ?></a>
																	</li>
																<?php endforeach; ?>
															</ul>
														</div>
													</div>
												<?php endif; ?>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
							<div class="no-job-data" style="display:none;">
								<div>
									<img src="<?php echo DISQO_TH_URL; ?>/assets/images/triangle-exclamation.svg" alt="" />
								</div>
								<div>
									<h3>Sorry, there are no results based on the keywords.</h3>
								</div>
								<div>
									<p>Try broadening your search terms.</p>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
<?php 
if( ! wp_script_is( 'job-list-js' ) ) {
	wp_register_script( 'job-list-js', DISQO_TH_URL ."/assets/js/job-list.js", array('jquery'), filemtime( DISQO_TH_PATH . '/assets/js/job-list.js' ), true );
	wp_enqueue_script( 'job-list-js' );
}
return ob_get_clean();
}
add_shortcode('open_roles', 'open_jobs');