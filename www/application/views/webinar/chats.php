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
$users = Arr::get($templateData, 'users', array());
$webinar_id = $templateData['webinar_id'];
$chats = $templateData['chats'];
?>
<style>
    .chat{width:12.1%;margin:0 2px;display: inline-block;float:left;}
    .user_id,.redirect_node_id{max-width:100%;width:100%}
    .chat-textarea{max-width: 90%;}
    .row{margin-left:0!important;}
</style>
<script>
    var urlBase = '<?php echo URL::base(true)?>';
    $(document).ready(function(){
        $( "#chats" ).sortable({
            connectWith: "#chats",
            handle: ".icon-move",
            update: function (event, ui) {
                saveChatsOrder($(this));
            }
        });

        var userLists = $('.user_id');
        userLists.on('change', function(){
            saveChosenUser($(this));
        });

        var ttalkButton = $('.ttalkButton');
        ttalkButton.on('click', function () {
            addChatMessage($(this), 0);
        });

        var ttalkRedirectButton = $('.ttalkRedirectButton');
        ttalkRedirectButton.on('click', function () {
            addChatMessage($(this), 0, 1);
        });
    });
</script>
<input type="hidden" value="<?php echo $webinar_id ?>" id="webinar_id">
<div id="chats" class="row">
    <?php foreach($chats as $chat_id => $v){ ?>

    <div class="panel chat ttalk" id="<?php echo $chat_id ?>">
        <div class="panel-heading">
            <div class="row">
                    <i class="icon icon-move" style="width:10%;"></i>
                <?php if(count($users) > 0){ ?>
                        <select class="user_id" style="display: inline-block;width:85%;">
                            <option value="">- choose User -</option>
                            <?php foreach($users as $user){ ?>
                                <option value="<?php echo $user->user->id ?>" <?php if(!empty($v['user_id']) && $user->user->id == $v['user_id']) echo 'selected'; ?>><?php echo $user->user->nickname ?></option>
                            <?php } ?>
                        </select>
                <?php } ?>
            </div>
        </div>
        <div class="panel-body">
            <div class="chat-window" style="height:450px;"></div>
            <div>
                <textarea class="chat-textarea ttalk-textarea" placeholder="Put your response..."></textarea>
                <button class="ttalkButton">Submit</button>
            </div>
            <div class="">
                <select class="redirect_node_id"></select>
                <button class="ttalkRedirectButton">Redirect!</button>
            </div>
            <div class="">
                <div>NodeId: <b class="node_id"></b></div>
                <div>Node Title: <b class="node_title"></b></div>
                <input type="hidden" class="session_id" value="">
                <input type="hidden" class="question_id" value="">
            </div>
        </div>
    </div>
        <script>
            $(document).ready(function(){
                setInterval(function() {
                    loadMessages('<?php echo $chat_id ?>', 0);
                }, 1500);

                setInterval(function() {
                    getLastNode('<?php echo $chat_id ?>');
                }, 1500);

                setInterval(function() {
                    getNodeLinks('<?php echo $chat_id ?>');
                }, 1500);
            });
        </script>
    <?php } ?>
</div>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/helper.js'); ?>"></script>
<script type="text/javascript" src="<?php echo ScriptVersions::get(URL::base().'scripts/turkTalk.js'); ?>"></script>