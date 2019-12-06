let addvm = new Vue({
    el: "#add-operation",
    data: {
		moviename:null,
		introduction:null,
		picked: '',
		selectedFile: ''
    },
	mounted(){
		this.init()
	},
    methods: {
		init: function(){
			const options = {
			         method: 'POST',
			         headers: { 'content-type': 'application/json' },
			         data: null,
			         url: 'addpage_init.php',
			 };
			  axios(options)
			  .then( (response) => {
				 if(response.data.success == true){
					document.getElementById('token').value = response.data.token;
				}else{
				    alert("System is busy!");
				}
			})
			.catch(error => console.error('Error:',error));
		},
		onFileChanged: function(event) {
		    this.selectedFile = event.target.files[0];
			console.log(this.selectedFile);
			console.log(this.selectedFile.name);
		  },
		add_database: function(){
			let my_token=document.getElementById('token').value;
			let filminfo = JSON.stringify({
			    moviename: this.moviename,
			    introduction: this.introduction,
				type: this.picked,
				filmurl: this.selectedFile.name,
				token:my_token
			});
			const options = {
			         method: 'POST',
			         headers: { 'content-type': 'application/json' },
			         data: filminfo,
			         url: 'insert_database.php',
			 };
			  axios(options)
			  .then( (response) => {
				 if(response.data.success == true){
					 alert("add movie successfully!");
				}else{
				    alert(response.data.message);
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
		}
    }
});



