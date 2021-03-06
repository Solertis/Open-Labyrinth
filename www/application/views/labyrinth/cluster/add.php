<?php
/**
 * Open Labyrinth [ http://www.openlabyrinth.ca ]
 *
 * Open Labyrinth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Labyrinth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Open Labyrinth.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright 2012 Open Labyrinth. All Rights Reserved.
 *
 */
if (isset($templateData['map'])) { ?>
   <div class="page-header"><h1><?php echo __('Add a new Labyrinth Data Cluster');?></h1></div>
                            <form class="form-horizontal" method='post' action='<?php echo URL::base(); ?>clusterManager/saveNewDam/<?php echo $templateData['map']->id; ?>'>
                                <fieldset class="fieldset">
                                    <div class="control-group">
                                        <label class="control-label" for="damname">Data cluster name</label>
                                        <div class="controls">
                                            <input type='text' class="span6" id="damname" name='damname'/>
                                        </div>
                                    </div>
                                </fieldset>
<div class="form-actions">
    <div class="pull-right">
        <input class="btn btn-primary btn-large" type='submit' value='Add' />
    </div>
</div>

                            </form>

<?php } ?>

