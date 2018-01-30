package com.mx.test;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.Toast;
import cn.pedant.SweetAlert.widget.SweetAlertDialog;

/** 登陆 */
public class LoginActivity extends Activity {
	private Button mLogin;
	private SweetAlertDialog mSettingDialog;

	@Override
	protected void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);

		setContentView(R.layout.activity_login);

		this.getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);

		findWidget();

		onClickEvents();
	}

	// 初始化ActionBar
	private void initActionBar() {

		// ImageButton arrowImageBtn = (ImageButton)
		// findViewById(R.id.image_arrow);
		// arrowImageBtn.setVisibility(View.VISIBLE);
		// arrowImageBtn.setOnClickListener(new OnClickListener() {
		// @Override
		// public void onClick(View v) {
		// mBaseApp.exit();// 退出App
		// }
		// });

		ImageButton imageBtn = (ImageButton) findViewById(R.id.imagBtn_setting);
		imageBtn.setVisibility(View.VISIBLE);
		imageBtn.setOnClickListener(new OnClickListener() {
			// SweetAlertDialog settingDialog;
			@Override
			public void onClick(View v) {

				mSettingDialog = new SweetAlertDialog(LoginActivity.this, false);

				mSettingDialog.setTitleText("设置考试网址");

				mSettingDialog.setContentText("例如：v3101.php56.dev.atapp.com");

				mSettingDialog.setCancelText("取消").setConfirmText("确定");

				mSettingDialog.show();
					}
				});
	}



	private void onClickEvents() {

		mLogin.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				Toast.makeText(LoginActivity.this,"登录成功",Toast.LENGTH_SHORT).show();
				Intent intent = new Intent(LoginActivity.this, MainActivity.class);
				intent.putExtra("studentname","张三");
				intent.putExtra("studentcode","A001");
				intent.putExtra("nextstudent","李四");
				startActivity(intent);
				finish();
			}
		});
	}



	private void findWidget() {
		mLogin = (Button) findViewById(R.id.button_login);
	}

}
