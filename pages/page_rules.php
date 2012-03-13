<?php
if(!isset($loadedProperly)) exit('File was not loaded properly.');

$page_title = 'Rules';
$page_content = '<h2>Rules</h2>
                <div class="charbox">
                    <div class="charbuttons" style="margin: 5px;">
                        <p style="font-size: 11px;">Although we allow everyone to play on '.$config->servername.' free of charge we have put up certain rules to maintain everyone\'s ability to enjoy the server on equal conditions.</p>
                        <p>The rules are listed below, and you must abide by them or you will recieve a punishment which the gamemaster sees fit. Punishments may include lowering your characters level and/or skills, banishment, and in severe cases a deletion.</p>

                        <p>Our rules include - but are not limited to - the following:</p>
                        <dl>
                        <dt style="font-weight: bold;">You may not..</dt>
                        <dd style="margin-left: 20px; line-height: 18px;">
                        ..use tools that allows your computer to play for you.
                        </dd>
                        <dd style="margin-left: 20px; line-height: 18px;">
                        ..share your account with other players.
                        </dd>
                        <dd style="margin-left: 20px; line-height: 18px;">
                        ..claim an area as yours.
                        </dd>
                        <dd style="margin-left: 20px; line-height: 18px;">
                        ..spam. If people choose not to reply, accept that.
                        </dd>
                        <dd style="margin-left: 20px; line-height: 18px;">
                        ..make non-english statements in public chats. (Default excluded)
                        </dd>
                        <dd style="margin-left: 20px; line-height: 18px;">
                        ..steal other players accounts.
                        </dd>
                        <dd style="margin-left: 20px; line-height: 18px;">
                        ..use bugs (errors) to gain an advantage over other players.
                        </dd>
                        <dd style="margin-left: 20px; line-height: 18px;">
                        ..block public areas such as depots, temples, and boats.
                        </dd>
                        <dd style="margin-left: 20px; line-height: 18px;">
                        ..pretend to be a representative of the '.$config->servername.' staff.
                        </dd>
                        </dl>
                    </div>
                </div>';

?>
