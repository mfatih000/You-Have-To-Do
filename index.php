<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_WARNING);

require 'db_conn.php';
$statuses = $conn->query("SELECT * FROM etiketler ")->fetchAll(PDO::FETCH_ASSOC);
$statuses_json = json_encode($etiketler);

if($_GET['operation']=='sil' and $_GET['id']<>''){
    $query = $conn -> prepare('DELETE FROM todos WHERE id=:id');
    $query->execute([
        ':id' => $_GET['id']
    ]);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>You Have To Do</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="main-section">
        <h2 id="main-title">YOU HAVE TO DO</h2>
       <div class="add-section">
          <form action="app/add.php" method="POST" autocomplete="off">
             <?php if(isset($_GET['mess']) && $_GET['mess'] == 'error'){ ?>
                <input type="text" 
                     name="title" 
                     style="border-color: #ff6666"
                     placeholder="This field is required" />
              <button type="submit">Add &nbsp; <span>&#43;</span></button>

             <?php }else{ ?>
              <input type="text" 
                     name="title" 
                     placeholder="What do you need to do?" />
              <button type="submit">Add &nbsp; <span>&#43;</span></button>
             <?php } ?>
          </form>
       </div>
       <?php 
          $todos = $conn->query("SELECT * FROM todos ORDER BY id DESC");
       ?>
       <div class="show-todo-section">
            <?php if($todos->rowCount() <= 0){ ?>
                <div class="todo-item">
                    
                </div>
            <?php } ?>

            <?php while($todo = $todos->fetch(PDO::FETCH_ASSOC)) { ?>
                <div class="todo-item">
                    <a href="index.php?operation=sil&id=<?php echo $todo['id']; ?>">
                        <span id="<?php echo $todo['id']; ?>"
                              class="remove-to-do">x</span>
                </a>
                    <?php if($todo['checked']){ ?> 
                        <input type="checkbox"
                               class="check-box"
                               data-todo-id ="<?php echo $todo['id']; ?>"
                               checked />
                        <h2 class="checked"><?php echo $todo['title'] ?></h2>
                    <?php }else { ?>
                        <input type="checkbox"
                               data-todo-id ="<?php echo $todo['id']; ?>"
                               class="check-box" />                      
                        <h2><?php echo $todo['title'] ?></h2>
                               
                    <?php } ?>
                    <br>
                    <small>created: <?php echo $todo['date_time'] ?></small>
                    
                    <div class="dropdown">
                        <button class="dropbtn">Etiket</button>
                        <div class="dropdown-content">
                            <a href="index.php?operation=sil&id=<?php echo $todo['id']; ?>">
                    
                            <?php foreach ($statuses as $status) { ?>
                                <a href="#" data-status="<?php echo htmlspecialchars(json_encode($status)); ?>">
                                    <?php echo $status['etiket_adi']; ?>
                                </a>
                                
                            <?php } ?>
                        </div>
                     </div>

                </div>
            <?php } ?>
       </div>
       <div class="add-tag">
        <form action="app/addTag.php" method="POST" autocomplete="off">
        <div class="row">
        <input type="text" id="tag-input" name="tag" placeholder="Enter tag name">
        <button type="submit">Add Tag</button>
        </div>
        </form>
       </div>
    </div>

    <script src="js/jquery-3.2.1.min.js"></script>

    <script>

        $(document).ready(function(){
            // $('.remove-to-do').click(function(){
            //     const id = $(this).attr('id');
                
            //     $.post("app/remove.php", 
            //           {
            //               id: id
            //           },
            //           (data)  => {
            //              if(data){
            //                  $(this).parent().hide(600);
            //              }
            //           }
            //     );
            // });

            $(".check-box").click(function(e){
                const id = $(this).attr('data-todo-id');
                
                $.post('app/check.php', 
                      {
                          id: id
                      },
                      (data) => {
                          if(data != 'error'){
                              const h2 = $(this).next();
                              if(data === '1'){
                                  h2.removeClass('checked');
                              }else {
                                  h2.addClass('checked');
                              }
                          }
                      }
                );
            });
        });
$(document).ready(function() {

  $('.dropbtn').on('click', function() {
    $(this).siblings('.dropdown-content').toggleClass('show');
  });

  $(document).on('click', function(e) {
    if (!$(e.target).hasClass('dropbtn')) {
      $('.dropdown-content').removeClass('show');
    }
  });
});

$('.dropdown-content a').on('click', function() {
            const statusText = $(this).text(); 
            const statusData = $(this).attr('data-status');

            if (statusData) {
                const statusObj = JSON.parse(statusData); 
            }

            $(this).closest('.dropdown').find('.dropbtn').text(statusText);

            $('.dropdown-content').removeClass('show');
        });



    </script>
</body>
</html>