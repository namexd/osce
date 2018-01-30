package com.mx.test.camera;

import android.app.Activity;
import android.net.Uri;

/**
 * 图片选取的回调(activity实现接口)
 *
 * @author Yuli 2015-5-11
 */
public interface PickHandler {

	/**
	 * 选取图片成功
	 *
	 * @param uri
	 */
	void onSuccess(Uri uri);

	/**
	 * 失败
	 */
	void onFailure();

	/**
	 * 返回裁剪的参数
	 *
	 * @return 如果返回null，则不裁剪，直接返回原图
	 */
	CropParams getCropParams();

	Activity getContext();

}
