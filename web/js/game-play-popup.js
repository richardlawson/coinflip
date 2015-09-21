function GamePlayPopup(games, finishedGameCode){
	
	this.games = games;
	this.FINISHED_GAME = finishedGameCode;
	this.noDialogs = 0;
	this.dialogX = 10;
	this.dialogY = 10;
	this.dialogOffsetX = 20;
	this.dialogOffsetY = 20;
	
	this.isMyFinishedGame = function(game){
		if(game.state == this.FINISHED_GAME && $.inArray(game.id, this.games) !== -1){
			return true;
	    }else{
	    	return false;
	    }
	}
	
	this.showMyGame = function(game){
    	var dialogHtml = '<div id="dialog-' + game.id + '"><iframe width="100%" height="100%" frameBorder="0" src="/secure/game-play/' + game.id + '?popup=true"></iframe></div>';
    	$('body').append(dialogHtml);
    	var dialogTitle = 'Game: ' + game.name;
    	var dialogWidth = this.getDialogWidth();
		var myPos = this.getDialogPosition();
    	$('#dialog-' + game.id).dialog({
    		title: dialogTitle,
			autoOpen: true,
			modal: false,
			width: dialogWidth,
			height: 500,
		    position: { my: myPos, at: "left top"}
		});
		this.noDialogs++;
    }
	
	this.getDialogWidth = function(){
    	var dialogWidth = $(window).width();
		if(dialogWidth < 650){
			dialogWidth *= 0.95;
		}else{
			dialogWidth = 600;
		}
		return dialogWidth;
    }
    
    this.getDialogPosition = function(){
		if(this.noDialogs > 0){
			this.dialogX += this.noDialogs * this.dialogOffsetX;
			this.dialogY += this.noDialogs * this.dialogOffsetY;
		}
		var posX = "left+" + this.dialogX;
		var posY = "top+" + this.dialogY;
		var myPos = posX + ' ' + posY;
		return myPos; 
    }
}
