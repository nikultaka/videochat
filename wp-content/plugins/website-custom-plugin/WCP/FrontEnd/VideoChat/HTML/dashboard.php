

  <style>
    body{
      margin: 0;
      box-sizing: border-box;
    }
    .game_wrapper{
      width: 100%;
      max-width: 100%;
      background: grey;
      text-align: center;
      height: 100vh;
   }
   .game_container{
      transform: translate(0, 100%);
    }
   .game-title{
    position: relative;
   }
   .game-title h3{
    color: #ad4033;
    font-size: 30px;
    width: 100%; 
    margin: 0 0 30px 0;
   }
   sup {
  font-size: 11px;
  position: relative;
  left: 3px;
  bottom: 12px;
   }
   .btn-container {
    display: flex;
    justify-content: center;
    width: 100%;
    max-width: 100%;
   }
   .game-link {
    color: #fff;
    font-size: 28px;
    font-weight: bold;
    text-decoration: none;
   }
   .join_link.game-link {
    border: 1px solid #ffffff4d;
    padding: 13px 10px;
    display: inline-block;
    min-width: 213px;
  }
   .new_game.game-link {
    background:  #ad4033;
    padding: 14px 10px;
    display: inline-block;
    min-width: 215px;
  }
@media(max-width: 768px){
  .btn-container {
    flex-direction: column;
  }
}
.entry-title {
  display: none;
}

</style>
  		
        <div class="game_container">
    			<div class="game-title">
    				<h3>Game Board</h3>
    			</div>
    			<div class="btn-container">
    				<div class="join_btn">
    				   <a href="<?php echo site_url('index.php/join-room'); ?>" class="join_link game-link">Join Game</a>
    			    </div>
    			    <div class="new_game_btn">
    				   <a href="<?php echo site_url('index.php/create-room'); ?>" class="new_game game-link">New Game</a>
    			    </div>
    			</div>
        </div>