var app = new Vue({
  el: '#app',
  data: {
    emptyPassword: false,
    submitable: true,
    mismatch: false,
    password: "",
    confirm_password: ""
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
      }else{
        this.submitable = false;
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
    }
  }
});
