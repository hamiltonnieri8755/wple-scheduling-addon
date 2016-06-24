

					<div class="postbox" id="SchedulingSettingsBox">
						<h3 class="hndle"><span><?php echo __('Incremental Scheduling','wple-scheduling-addon') ?></span></h3>
						<div class="inside">

							<label for="wpl-scheduling_auto_increment" class="text_label">
								<?php echo __('Auto increment','wple-scheduling-addon'); ?>
                                <?php wplister_tooltip('Please note that the auto imcrement option will only be applied if the minute part of the stored time is 00.') ?>
							</label>
							<select id="wpl-scheduling_auto_increment" name="wple_scheduling_auto_increment" class="required-entry select">
								<option value="0"  <?php if ( $wpl_scheduling_auto_increment == '0'  ): ?>selected="selected"<?php endif; ?>><?php  echo __('Off','wple-scheduling-addon'); ?></option>
								<option value="1"  <?php if ( $wpl_scheduling_auto_increment == '1'  ): ?>selected="selected"<?php endif; ?>>1  <?php  echo __('minute','wple-scheduling-addon'); ?></option>
								<option value="2"  <?php if ( $wpl_scheduling_auto_increment == '2'  ): ?>selected="selected"<?php endif; ?>>2  <?php  echo __('minutes','wple-scheduling-addon'); ?></option>
								<option value="3"  <?php if ( $wpl_scheduling_auto_increment == '3'  ): ?>selected="selected"<?php endif; ?>>3  <?php  echo __('minutes','wple-scheduling-addon'); ?></option>
								<option value="5"  <?php if ( $wpl_scheduling_auto_increment == '5'  ): ?>selected="selected"<?php endif; ?>>5  <?php  echo __('minutes','wple-scheduling-addon'); ?></option>
								<option value="10" <?php if ( $wpl_scheduling_auto_increment == '10' ): ?>selected="selected"<?php endif; ?>>10 <?php echo __('minutes','wple-scheduling-addon'); ?></option>
							</select>
							<p class="desc" style="display: block;">
								<?php echo __('Enable this to automatically increment the schedule time with each listed item.','wplister'); ?>
							</p>

							<label for="wpl-scheduling_max_increment" class="text_label">
								<?php echo __('Maximum delay','wple-scheduling-addon'); ?>
                                <?php wplister_tooltip('Example: You set auto increment to 2 min. and max. delay to 60 min. - then listings scheduled for 20:00 will start at 20:02, 20:04, 20:06, ... 20:58, 21:00.<br><br>The default is 60 minutes.') ?>
							</label>
							<select id="wpl-scheduling_max_increment" name="wple_scheduling_max_increment" class="required-entry select">
								<option value="30"  <?php if ( $wpl_scheduling_max_increment == '30'  ): ?>selected="selected"<?php endif; ?>>30  <?php  echo __('minutes','wple-scheduling-addon'); ?></option>
								<option value="60"  <?php if ( $wpl_scheduling_max_increment == '60'  ): ?>selected="selected"<?php endif; ?>>60  <?php  echo __('minutes','wple-scheduling-addon'); ?></option>
								<option value="120" <?php if ( $wpl_scheduling_max_increment == '120' ): ?>selected="selected"<?php endif; ?>>120 <?php echo __('minutes','wple-scheduling-addon'); ?></option>
							</select>
							<p class="desc" style="display: block;">
								<?php echo __('Select the maximum delay when auto increment is enabled.','wplister'); ?>
							</p>

						</div>
					</div>
