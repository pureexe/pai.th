var app = new Vue({
  el: '#app',
  data: {
    username: "",
    usernameNotAccept: false,
    emptyPassword: false,
    submitable: true,
    mismatch: false,
    password: "",
    confirm_password: "",
    confirm_password_text: "ยืนยันรหัสผ่านอีกครั้ง"
  },
  methods: {
    onSubmit: function(event){
      if(this.password.trim() == ""){
        this.emptyPassword = true;
        this.submitable = false;
        event.preventDefault();
      }else if(this.password.trim() != this.confirm_password.trim()){
        this.mismatch = true;
        this.submitable = false;
        event.preventDefault();
      }else if(usernameNotAccept){
        event.preventDefault();
      }else{
        this.submitable = false;
      }
    },
    onUsernameKeyup: function(){
      if(this.username.match(/^[a-zA-Z0-9]+$/) == null){
        this.usernameNotAccept = true;
      }else if(this.username.trim().length == 0){
        this.usernameNotAccept = true;
      }else{
        this.usernameNotAccept = false;
      }
    },
    onKeyup: function(){
      if(this.confirm_password.trim() == ""){
        this.emptyPassword = false;
        this.mismatch = false;
        this.submitable = true;
      }else if(this.confirm_password.trim() != this.password.trim()){
        this.emptyPassword = false;
        this.mismatch = true;
        this.submitable = false;
      }else{
        this.emptyPassword = false;
        this.mismatch = false;
        this.submitable = true;
      }
      if(this.confirm_password.length == 0){
        this.confirm_password_text = "ยืนยันรหัสผ่านอีกครั้ง";
      }else{
        this.confirm_password_text = "";
      }
    }
  }
});
