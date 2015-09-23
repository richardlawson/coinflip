function CoinAnimation(headsWins, winnerId){
	this.headsWins = headsWins;
	this.winnerId = winnerId;
	this.tid;
	this.count = 0;
	this.maxFlips = 10;
	var that = this;
	
	this.init = function(){
		$("#coin-heads").show();
		$("#coin-tails").hide();
		this.tid = setInterval(this._doCoinAnimation, 1000);
	}
	
	this._doCoinAnimation = function(){
		$("#coin-heads").toggle();
		$("#coin-tails").toggle();
		that.count++;
		if(that.count >= that.maxFlips){
			that._endAnimationAndShowWinner();
		}
	}
			
	this._endAnimationAndShowWinner = function(){
		clearInterval(this.tid);
		if(this.headsWins){
			$("#coin-heads").show();
			$("#coin-tails").hide();
		}else{
			$("#coin-heads").hide();
			$("#coin-tails").show();
		}
		$("#progress-bar").text('Game Finished');
		$("#winner-panel").show();
		var winnerStar = $(".star-" + this.winnerId);
		winnerStar.attr("src", '/images/star_on.png');
	}
}
