<div id='newsfeed' class='leftMenu'>
   <ul id='newsfeedList'>
      <li id='newPost'>
      <div>Add New Post Here</div>
      <? if($mobile === true): ?>
      <textarea rows='6' cols='36' id='newPostTextArea'></textarea>
      <? else: ?>
      <textarea rows='6' cols='30' id='newPostTextArea'></textarea>
      <? endif; ?>
      <br>
      <button id='addNewPost' onclick='addNewPost();'>Submit</button>
      </li>
      <br style='clear:left'>
      <? foreach ($posts as $key => $value): ?>
      <li id='message<? echo $value['messageId']?>' >
         <div>
            <? echo $value['employee'] ?>,
            <small><? echo $value['datePosted'] ?></small>
         </div>
         <? if($mobile === true): ?>
         <textarea rows="10" cols="36"><? echo $value['message'] ?>
         <? else: ?>
         <textarea rows="6" cols="30"><? echo $value['message']?>
         <? endif; ?>
         </textarea>
         <br style='clear:left'>
         <button id='submit<? echo $value['messageId']?>' class='mainButton' onclick='updatePost(<? echo $value['messageId']?>);'>Update</button>
         <button id='delete'<? echo $value['messageId']?>' class='mainButton' onclick='deletePost(<? echo $value['messageId'] ?>);'>Delete</button>
         <br style='clear:left'>     
      </li>
      <? endforeach; ?>
   </ul>
</div>
