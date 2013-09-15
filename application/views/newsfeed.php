<div id='newsfeed' class='leftMenu hiddenPrint'>
   <ul id='newsfeedList'>
      <li id='newPost'>
         <div class='form-group'>
         <? if($mobile === true): ?>
            <textarea class='form-control' rows='6' cols='36' id='newPostTextArea' placeholder='Type here to add a new post...'></textarea>
         <? else: ?>
            <textarea class='form-control' rows='5' cols='31' id='newPostTextArea' placeholder='Type here to add a new post...'></textarea>
         <? endif; ?>
         </div>
         <a id='addNewPost' class='btn btn-small btn-default pull-right' onclick='addNewPost();'>Submit</a>
         <br class='clearfix'>
         <hr>
      </li>
      <? foreach ($posts as $key => $value): ?>
      <li id='message<? echo $value['messageId']?>' >
         <div class='well'><? echo $value['message']?></div>
         <div class='pull-right'>
            <span class='label label-big'><? echo $value['employee'] ?></span> <span class='label label-big'><? echo $value['datePosted'] ?></span>
            <a id='delete<? echo $value['messageId']?>' class='btn btn-small btn-danger' onclick='deletePost(<? echo $value['messageId'] ?>);'>Delete</a>
         </div>
         <br class='clearfix'>
         <hr>  
      </li>
      <? endforeach; ?>
   </ul>
</div>

