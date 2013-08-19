<div id='newsfeed' class='leftMenu hiddenPrint'>
   <ul id='newsfeedList'>
      <li id='newPost'>
         <span>Add New Post Here</span>
         <? if($mobile === true): ?>
         <textarea rows='6' cols='36' id='newPostTextArea'></textarea>
         <? else: ?>
         <textarea rows='5' cols='31' id='newPostTextArea'></textarea>
         <? endif; ?>
         <br style='clear:left;'>
         <a id='addNewPost' class='btn btn-small btn-default' onclick='addNewPost();'>Submit</a>
         <hr>
      </li>
      <? foreach ($posts as $key => $value): ?>
      <li id='message<? echo $value['messageId']?>' >
         <div>
            <span class='label'><? echo $value['employee'] ?>,
            <small><? echo $value['datePosted'] ?></small>
            </span>
         </div>
         <br>
         <? if($mobile === true): ?>
         <textarea rows="10" cols="36"><? echo $value['message'] ?></textarea>
         <? else: ?>
         <div class='well'><? echo $value['message']?></div>
         <? endif; ?>
         <a id='delete<? echo $value['messageId']?>' class='btn btn-small btn-danger' onclick='deletePost(<? echo $value['messageId'] ?>);'>Delete</a>
         <hr>  
      </li>
      <? endforeach; ?>
   </ul>
</div>

