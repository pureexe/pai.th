<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.blue-light_blue.min.css" />
    <link href="https://fonts.googleapis.com/css?family=Athiti" rel="stylesheet">
    <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
    <link rel="stylesheet" href="/assets/css/invite_register.css" />
  </head>
  <body>
    <h1>ไป.ไทย</h1>
    <div class="card mdl-card mdl-shadow--2dp" id="app">
      <form action="" method="post" v-on:submit="onSubmit">
        <?php if(!empty($username)): ?>
        <div class="mdl-textfield mdl-js-textfield">
          <span>ชื่อผู้ใช้:</span> <span><?=$username?></span>
          <input type="hidden" v-model="username" value="a">
        </div>
        <?php else: ?>
        <div v-bind:class="{'is-invalid':usernameNotAccept,'mdl-textfield':true,'mdl-js-textfield':true,'mdl-textfield--floating-label':true}">
          <input class="mdl-textfield__input" type="text" id="username" name="username" v-model="username" v-on:keyup="onUsernameKeyup">
          <label class="mdl-textfield__label" for="username">ชื่อผู้ใช้</label>
        </div>
        <?php endif; ?>
        <div v-bind:class="{'is-invalid':emptyPassword,'mdl-textfield':true,'mdl-js-textfield':true,'mdl-textfield--floating-label':true}">
          <input class="mdl-textfield__input" type="password" id="password" name="password" v-model="password" v-on:keyup="onKeyup">
          <label class="mdl-textfield__label" for="password">ตั้งรหัสผ่าน</label>
        </div>
        <div v-bind:class="{'is-invalid':mismatch,'mdl-textfield':true,'mdl-js-textfield':true,'mdl-textfield--floating-label':true}">
          <input class="mdl-textfield__input" type="password" id="confirm_password" name="confirm_password" v-model="confirm_password" v-on:keyup="onKeyup" >
          <label class="mdl-textfield__label" for="confirm_password">ยืนยันรหัสผ่านอีกครั้ง</label>
        </div>
        <input type="hidden" name="invite_token" value="<?=$invite_token?>" />
        <?php if(!empty($username)): ?>
          <div style="display:none">
            {{acceptTOS = true}}
          </div>
        <?php else: ?>
        <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="acceptTOS" style="margin-top:10px;margin-bottom:20px;">
          <input type="checkbox" id="acceptTOS" class="mdl-checkbox__input" v-model="acceptTOS">
          <span class="mdl-checkbox__label">ยอมรับ <a href="/ข้อตกลง" target="_blank">ข้อตกลงการใช้งาน</a></span>
        </label>
        <?php endif; ?>
        <center>
          <p class="red" v-show="mismatch">รหัสผ่านไม่ตรงกัน</p>
          <p class="red" v-show="emptyPassword">กรุณากรอกรหัสผ่าน</p>
          <p class="red" v-show="usernameNotAccept">ชื่อผู้ใช้ต้องเป็นภาษาอังกฤษและตัวเลขเท่านั้น</p>
          <button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" :disabled="!submitable || usernameNotAccept || !acceptTOS">
            สมัครสมาชิก
          </button>
        </center>
      </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.3.3/vue.min.js"></script>
    <script src="/assets/js/invite_register.js"></script>
  </body>
</html>
