let navigatevm = new Vue({
    el: "#homepage",
    data: {
		films: [],
		content:null,
		judge:[]
    },
	mounted(){
		this.init()
	},
    methods: {
        log_out:function() {
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
		choose:function(type){
			let my_token=document.getElementById('token').value;
			const options = {
			        method: 'POST',
			        headers: { 'content-type': 'application/json' },
			        data: {types:type,token:my_token},
			        url: 'choosetype.php',
			};
			axios(options)
			.then( (response) => {
			      if(response.data.success == true){
					  this.films.splice(0,this.films.length);
					  for (let i=0; i < response.data.datas.length; i++){
					      let moviename=response.data.datas[i].movie_name;
					      let pictureurl="../"+response.data.datas[i].picture_url;
						  let stars=response.data.datas[i].stars;
						  let adjudge=response.data.datas[i].adjudge;
					      let film=({name:moviename,url:pictureurl,stars:stars,judge:adjudge})
					  	  this.films.push(film);
					  }
			      }else{
			      	alert(response.data.message);
			      }
			})
			.catch(error => console.error('Error:',error));
		},
		init: function () {
			  const options = {
			           method: 'POST',
			           headers: { 'content-type': 'application/json' },
			           data: null,
			           url: 'renderingmovie.php',
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
						let filmurl=response.data.datas[i].picture_url;
						let stars=response.data.datas[i].stars;
						let adjudge=response.data.datas[i].adjudge;
			  		    let film=({name:moviename,filmurl:filmurl,url:pictureurl,stars:stars,judge:adjudge});
						this.judge.push(adjudge);
						this.films.push(film);
			  		}
			  	}else{
			  	    alert("rendering failed!");
			  	}
			  })
			  .catch(error => console.error('Error:',error));
		  },
		  searching: function () {
			  let my_token=document.getElementById('token').value;
		  	  const options = {
		  	           method: 'POST',
		  	           headers: { 'content-type': 'application/json' },
		  	           data: {contents:this.content,token:my_token},
		  	           url: 'search.php'
		  	   };
		  	    axios(options)
		  	    .then( (response) => {
		  			 this.films.splice(0,this.films.length);
		  	  		 for (let i=0; i < response.data.datas.length; i++){
		  	  		    let moviename=response.data.datas[i].movie_name;
		  	  		    let pictureurl="../"+response.data.datas[i].picture_url;
						let stars=response.data.datas[i].stars;
						let adjudge=response.data.datas[i].adjudge;
		  	  		    let film=({name:moviename,url:pictureurl,stars:stars,judge:adjudge})
		  				this.films.push(film);
		  	  		}
		  	  })
		  	  .catch(error => console.error('Error:',error));
		    },
		go: function(e){
			let my_token=document.getElementById('token').value;
			const options = {
			         method: 'POST',
			         headers: { 'content-type': 'application/json' },
			         data: {filmname:e,token:my_token},
			         url: 'filmsession.php'
			 };
			  axios(options)
			  .then( (response) => {
			  	  if(response.data.success == true){
			  		  window.location.href = '../comment/filmcomment.html';
			  	  }
			  })
			  .catch(error => console.error('Error:',error));
		},
		deletefilm: function(e,m){
			let my_token=document.getElementById('token').value;
			const options = {
			         method: 'POST',
			         headers: { 'content-type': 'application/json' },
			         data: {filmname:e,filmurl:m,token:my_token},
			         url: 'deletefilm.php'
			 };
			  axios(options)
			  .then( (response) => {
				  if(response.data.success == true){
					  this.init();
				  }
			})
			.catch(error => console.error('Error:',error));
		},
		addmovie:function(){
			window.location.href = '../operation/addmovie.html';
		}
    }
});



