package com.mx.osce.camera;

import android.app.Activity;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;

/**
 * 选择图片的aty Create by Yuli on 2015-6-1
 */

// 使用方法：
// public void btnPicker(View v) {
// Intent in = new Intent(this, PhotoPicker.class);
// in.putExtra("needCrop", false); // 是否需要裁剪
// startActivityForResult(in, PhotoPicker.DEFAULT_REQUEST_CODE);
// }
//
// @Override
// protected void onActivityResult(int requestCode, int resultCode, Intent data)
// {
// super.onActivityResult(requestCode, resultCode, data);
//
// if (requestCode == PhotoPicker.DEFAULT_REQUEST_CODE) {
// switch (resultCode) {
// case RESULT_CANCELED:
// // TODO 失败的回调
// break;
// case RESULT_OK:
// // TODO 成功
// Uri uri = data.getParcelableExtra("ImageUri");
// // String path = CropHelper.saveImg2SD(this, uri, "img");// 存放到本地
// Bitmap photo = CropHelper.decodeUriAsBitmap(this, uri);// 解析成bitmap
// ivImg.setImageBitmap(photo);
// break;
// }
// }
// }
public class PhotoPicker extends Activity implements PickHandler {

	private CropParams cropParams;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		requestWindowFeature(Window.FEATURE_NO_TITLE);
		// setTheme(R.style.ChoosePicDialog);
		// setContentView(R.layout.dialog_choose_pic);

		// setContentView(R.layout.choose_pic_dia);

		cropParams = new CropParams();

		// 解决dialog样式不能全屏的问题
		getWindow().setLayout(ViewGroup.LayoutParams.MATCH_PARENT, ViewGroup.LayoutParams.WRAP_CONTENT);
		getWindow().setGravity(Gravity.BOTTOM);

	}

	public void btnCamera(View v) {
		startActivityForResult(CropHelper.buildCaptureIntent(), CropHelper.REQUEST_CAMERA);
	}

	public void btnGallery(View v) {
		startActivityForResult(CropHelper.buildGalleryIntent(), CropHelper.REQUEST_GALLERY);
	}

	public void btnCancel(View v) {
		setResult(RESULT_CANCELED);
		this.finish();
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);
		CropHelper.handleResult(this, requestCode, resultCode, data);
	}

	@Override
	public void onSuccess(Uri uri) {
		// String path = CropHelper.saveImg2SD(this, mCropParams.uri, "img");
		// Bitmap photo = CropHelper.decodeUriAsBitmap(this, mCropParams.uri);

		setResult(RESULT_OK, new Intent().putExtra("ImageUri", uri));
		this.finish();
	}

	@Override
	public void onFailure() {
		setResult(RESULT_CANCELED);
		this.finish();
	}

	@Override
	public CropParams getCropParams() {
		return getIntent().getBooleanExtra("needCrop", true) ? cropParams : null;
	}

	@Override
	public Activity getContext() {
		return this;
	}

	@Override
	protected void onDestroy() {
		if (getCropParams() != null) // 清除裁剪图片的数据
			CropHelper.clearCachedFile(getCropParams().uri);
		super.onDestroy();
	}

}
