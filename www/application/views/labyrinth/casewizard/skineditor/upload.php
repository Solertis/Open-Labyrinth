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
    <table width="100%" height="100%" cellpadding="6">
        <tr>
            <td valign="top" bgcolor="#bbbbcb">
                <h4><?php echo __('Upload skin for "').$templateData['map']->name.'"'; ?></h4>
                <table width="100%" cellpadding="6">
                    <tr bgcolor="#ffffff"><td align="left">
                            <form id="form1" name="form1" method="post" enctype="multipart/form-data" action="<?php echo URL::base().'labyrinthManager/caseWizard/5/uploadNewSkin/'.$templateData['map']->id; ?>">
                                <table bgcolor="#ffffff" cellpadding="6" width="80%">
                                    <tr>
                                        <td style="width:100px;">
                                            <p><?php echo __('Select skin file (.zip)'); ?></p>
                                        </td>
                                        <td>
                                            <input type="file" name="zipSkin" value="" />
                                        </td>
                                    </tr>

                                    <tr><td colspan="2"><input type="submit" name="Submit" value="<?php echo __('submit'); ?>"></td></tr>
                                </table>
                            </form>
                            <br>
                            <br>
                        </td></tr>
                </table>
            </td>
        </tr>
    </table>
<?php } ?>