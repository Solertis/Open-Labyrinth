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
<div class="page-header">
<h1><?php if(!isset($templateData['question'])){
        echo __('New question for"') . $templateData['map']->name . '"';
    } else {
        echo __('Edit question "') . $templateData['question']->stem . '"'; }?>
</h1></div>
<form class="form-horizontal" 
      method="POST" 
      action="<?php echo URL::base(); ?>questionManager/questionPOST/<?php echo $templateData['map']->id; ?>/<?php echo $templateData['type']->id; ?><?php echo (isset($templateData['question']) ? ('/' . $templateData['question']->id) : ''); ?>">

    <fieldset>
        <div class="control-group">
            <label for="qstem" class="control-label"><?php echo __('Stem'); ?>
            </label>
            <div class="controls">
                <textarea id="qstem" name="qstem"><?php if(isset($templateData['question'])) echo $templateData['question']->stem; ?></textarea>
            </div>
        </div>
        <div class="control-group">
            <label for="qwidth" class="control-label"><?php echo __('Width'); ?>
            </label>
            <div class="controls">
                <select  id="qwidth" name="qwidth">
                    <?php for($i = 10; $i <= 60; $i += 10) { ?>
                        <option value="<?php echo $i; ?>" <?php if(isset($templateData['question']) and $templateData['question']->width == $i) echo 'selected=""'; ?>><?php echo $i; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label for="fback" class="control-label"><?php echo __('Prompt text'); ?>
            </label>
            <div class="controls">

                <textarea id="fback" name="fback"><?php if(isset($templateData['question'])) echo $templateData['question']->feedback; ?></textarea>
            </div>
        </div>
    </fieldset>
    <div class="form-actions">
        <div class="pull-right">
            <input class="btn btn-large btn-primary" type="submit" name="Submit" value="Save">
        </div>
    </div>
</form>
<?php } ?>


