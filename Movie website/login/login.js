//login
let loginvm = new Vue({
    el: "#login-wrap",
    data: {
        username: null,
        password: null,
    },
    methods: {
        login:function() {
            let personalinfo = JSON.stringify({
                username: this.username,
                password: this.password,
            });
			const options = {
			        method: 'POST',
			        headers: { 'content-type': 'application/json' },
			        data: personalinfo,
			        url: 'login.php',
			};
            axios(options)
            .then( function(response) {
                  if(response.data.success == true){
					window.location.href = '../homepage/homepage.html';
                  }else{
                  	alert(response.data.message);
                  }
            })
            .catch(error => console.error('Error:',error));
        }
    }
});

//adlogin
let adloginvm = new Vue({
    el: "#adlogin-wrap",
    data: {
        adusername: null,
        adpassword: null,
    },
    methods: {
        adlogin:function() {
			console.log(this.adusername);
			let adpersonalinfo = JSON.stringify({
			    adusername: this.adusername,
			    adpassword: this.adpassword,
			});
			const options = {
			        method: 'POST',
			        headers: { 'content-type': 'application/json' },
			        data: adpersonalinfo,
			        url: 'adlogin.php',
			};
            axios(options)
            .then( function(response) {
                  if(response.data.success == true){
					window.location.href = '../homepage/homepage.html';
                  }else{
                  	alert(response.data.message);
                  }
            })
            .catch(error => console.error('Error:',error));
        }
    }
});

//register  
let registervm = new Vue({
    el: "#register-wrap",
    data: {
        registername: null,
        registerpassword: null
    },
    methods: {
        register:function() {
            let registerinfo = JSON.stringify({
                registername: this.registername,
                registerpassword: this.registerpassword
            });
			const options = {
			        method: 'POST',
			        headers: { 'content-type': 'application/json' },
			        data: registerinfo,
			        url: 'register.php',
			};
            axios(options)
            .then( function(response) {
                  if(response.data.success == true){
					alert("register success!");
                  }else{
                  	alert(response.data.message);
                  }
            })
            .catch(error => console.error('Error:',error));
        }
    }
});
  