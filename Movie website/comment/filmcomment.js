let commentvm = new Vue({
    el: "#filmcomment",
    data: {
		films:[],
		comments:[],
		judge:[],
		picked: null,
		discuss:null
    },
	mounted(){
		this.init();
		this.load();
	},
    methods: {
		init: function () {
			  const options = {
			           method: 'POST',
			           headers: { 'content-type': 'application/json' },
			           data: null,
			           url: 'movieinfo.php',
			   };
			    axios(options)
			    .then( (response) => {
			  	 if(response.data.success == true){
					 this.films.splice(0,this.films.length);
					 this.judge.splice(0,this.judge.length);
					 document.getElementById('token').value = response.data.token;
			  		 for (let i=0; i < response.data.datas.length; i++){
			  		    let moviename=response.data.datas[i].movie_name;
			  		    let pictureurl="../"+response.data.datas[i].picture_url;
						let stars=response.data.datas[i].stars;
						let introduction=response.data.datas[i].introduction;
						let adjudge=response.data.datas[i].adjudge;
			  		    let film=({name:moviename,url:pictureurl,stars:stars,judge:adjudge,intro:introduction});
						this.judge.push(adjudge);
						this.films.push(film);
			  		}
			  	}else{
			  	    alert("loading failed!");
			  	}
			  })
			  .catch(error => console.error('Error:',error));
		  },
		load: function () {
			  const options = {
			           method: 'POST',
			           headers: { 'content-type': 'application/json' },
			           data: null,
			           url: 'commentload.php',
			   };
			    axios(options)
			    .then( (response) => {
			  	 if(response.data.success == true){
					 this.comments.splice(0,this.comments.length);
			  		 for (let i=0; i < response.data.datas.length; i++){
			  		    let moviename=response.data.datas[i].moviename;
			  		    let username=response.data.datas[i].username;
						let contents=response.data.datas[i].contents;
						let likes=response.data.datas[i].likes;
						let time=response.data.datas[i].time;
						let id=response.data.datas[i].id;
						let points=response.data.datas[i].points;
						let number=i+1;
						let comment=({number:number,username:username,contents:contents,likes:likes,time:time,id:id,points:points});
						this.comments.push(comment);
			  		}
			  	}else{
			  	    alert("loading failed!");
			  	}
			  })
			  .catch(error => console.error('Error:',error));
		  },
		add_comment: function(){
			let my_token=document.getElementById('token').value;
			let commentinfo = JSON.stringify({
			    discuss: this.discuss,
				token:my_token
			});
			const options = {
			         method: 'POST',
			         headers: { 'content-type': 'application/json' },
			         data: commentinfo,
			         url: 'comment_database.php'
			 };
			  axios(options)
			  .then( (response) => {
				  console.log(response);
				 if(response.data.success == true){
					this.init();
					this.load();
					alert("You have successfully comment!");
				}else{
				    alert(response.data.message);
				}
			})
			.catch(error => console.error('Error:',error));
		},
		sort_comment: function(){
			let my_token=document.getElementById('token').value;
			const options = {
			         method: 'POST',
			         headers: { 'content-type': 'application/json' },
			         data: {token:my_token},
			         url: 'sort_comment.php'
			 };
			  axios(options)
			  .then( (response) => {
				 if(response.data.success == true){
				 	 this.comments.splice(0,this.comments.length);
				 	 for (let i=0; i < response.data.datas.length; i++){
				 	    let moviename=response.data.datas[i].moviename;
				 	    let username=response.data.datas[i].username;
				 		let contents=response.data.datas[i].contents;
				 		let likes=response.data.datas[i].likes;
				 		let time=response.data.datas[i].time;
				 		let id=response.data.datas[i].id;
						let points=response.data.datas[i].points;
				 		let number=i+1;
				 	    let comment=({number:number,username:username,contents:contents,likes:likes,time:time,id:id,points:points});
				 		this.comments.push(comment);
				 	}
				 }else{
				     alert("loading failed!");
				 } 
			})
			.catch(error => console.error('Error:',error));
		},
		thumbup: function(e){
			let my_token=document.getElementById('token').value;
			const options = {
			         method: 'POST',
			         headers: { 'content-type': 'application/json' },
			         data: {comment_id:e,token:my_token},
			         url: 'thumbup.php'
			 };
			  axios(options)
			  .then( (response) => {
			  	  if(response.data.success == true){
			  		  this.init();
			  		  this.load();
			  	  }
			  })
			  .catch(error => console.error('Error:',error));
		},
		remove: function(e){
			let my_token=document.getElementById('token').value;
			const options = {
			         method: 'POST',
			         headers: { 'content-type': 'application/json' },
			         data: {comment_id:e,token:my_token},
			         url: 'remove_comment.php'
			 };
			  axios(options)
			  .then( (response) => {
			  	  if(response.data.success == true){
			  		  this.init();
			  		  this.load();
			  	  }else{
					  alert(response.data.message);
				  }
			  })
			  .catch(error => console.error('Error:',error));
		},
		logout:function() {
			const options = {
			        method: 'POST',
			        headers: { 'content-type': 'application/json' },
			        data: null,
			        url: 'logout.php',
			};
		    axios(options)
		    .then( function(response) {
		          if(response.data.success == true){
					window.location.href = '../login/login.html';
		          }else{
		          	alert("logout failed!");
		          }
		    })
		    .catch(error => console.error('Error:',error));
		},
		back: function(){
			const options = {
			         method: 'POST',
			         headers: { 'content-type': 'application/json' },
			         data: null,
			         url: 'goback.php',
			 };
			  axios(options)
			  .then( (response) => {
				 if(response.data.success == true){
					 window.location.href = '../homepage/homepage.html';
				}else{
				    alert("system is busy!");
				}
			})
			.catch(error => console.error('Error:',error));
		},
		rate: function(){
			let my_token=document.getElementById('token').value;
			let pointsinfo = JSON.stringify({
				points:this.picked,
				token:my_token
			});
			const options = {
			         method: 'POST',
			         headers: { 'content-type': 'application/json' },
			         data: pointsinfo,
			         url: 'rating.php'
			 };
			  axios(options)
			  .then( (response) => {
				  console.log(response);
			  	  if(response.data.success == true){
					  alert(response.data.message);
					  this.init();
					  this.load();
			  	  }else{
					  alert(response.data.message);
				  }
			  })
			  .catch(error => console.error('Error:',error));
		}
    }
});



