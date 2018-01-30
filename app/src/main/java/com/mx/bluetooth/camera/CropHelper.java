package com.mx.bluetooth.camera;

import android.annotation.TargetApi;
import android.app.Activity;
import android.content.ContentUris;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Build;
import android.os.Environment;
import android.provider.DocumentsContract;
import android.provider.MediaStore;
import android.util.Log;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.InputStream;

/**
 * 剪切图片helper
 *
 * @author Yuli 2015-5-11
 */
public class CropHelper {

	public static final String TAG = "CropHelper";
	public static final int REQUEST_CROP = 127;
	public static final int REQUEST_CAMERA = 128;
	public static final int REQUEST_GALLERY = 129;
	public static final String CAMERA_CACHE_FILE_NAME = "tempFileFc.jpg";
	public static final Uri CAMERA_URI = Uri.fromFile(Environment.getExternalStorageDirectory()).buildUpon()
			.appendPath(CAMERA_CACHE_FILE_NAME).build();

	public static void handleResult(PickHandler handler, int requestCode, int resultCode, Intent data) {

		if (handler == null)
			return;

		if (resultCode == Activity.RESULT_CANCELED) {
			handler.onFailure();
		} else if (resultCode == Activity.RESULT_OK) {

			Activity context = handler.getContext();

			switch (requestCode) {
			case REQUEST_CROP:
				handler.onSuccess(handler.getCropParams().uri);
				break;
			case REQUEST_CAMERA:
				if (null != handler.getCropParams()) {
					Intent intent = buildCropIntent(handler.getCropParams(), CAMERA_URI);
					context.startActivityForResult(intent, REQUEST_CROP);
				} else {
					handler.onSuccess(CAMERA_URI);
				}
				break;
			case REQUEST_GALLERY:
				String path = getRealPathFromURIKK(context, data.getData());
				if (null != handler.getCropParams()) {
					Intent intent2 = buildCropIntent(handler.getCropParams(), Uri.fromFile(new File(path)));
					context.startActivityForResult(intent2, REQUEST_CROP);
				} else {
					handler.onSuccess(Uri.fromFile(new File(path)));
				}
				break;
			}
		}
	}

	public static boolean clearCachedFile(Uri uri) {
		if (uri == null)
			return false;

		File file = new File(uri.getPath());
		if (file.exists()) {
			boolean result = file.delete();
			if (result)
				Log.e(TAG, "Cached file cleared.");
			else
				Log.e(TAG, "Failed to clear cached file.");
			return result;
		} else {
			Log.e(TAG, "Trying to clear cached file but it does not exist.");
		}
		return false;
	}

	public static Intent buildGalleryIntent() {
		return new Intent(Intent.ACTION_GET_CONTENT).addCategory(Intent.CATEGORY_OPENABLE).setType("image/*");
	}

	public static Intent buildCaptureIntent() {
		return new Intent(MediaStore.ACTION_IMAGE_CAPTURE).putExtra(MediaStore.EXTRA_OUTPUT, CAMERA_URI);
	}

	public static Intent buildCropIntent(CropParams params, Uri data) {
		return new Intent("com.android.camera.action.CROP", null).setDataAndType(data, params.type)// 输入
				.putExtra("crop", params.crop).putExtra("scale", params.scale).putExtra("aspectX", params.aspectX)
				.putExtra("aspectY", params.aspectY).putExtra("outputX", params.outputX)
				.putExtra("outputY", params.outputY).putExtra("return-data", params.returnData)
				.putExtra("outputFormat", params.outputFormat).putExtra("noFaceDetection", params.noFaceDetection)
				.putExtra("scaleUpIfNeeded", params.scaleUpIfNeeded).putExtra(MediaStore.EXTRA_OUTPUT, params.uri);// 输出
	}

	/**
	 * 获取到裁剪后图片的uri调用该方法解析成bitmap
	 */
	public static Bitmap decodeUriAsBitmap(Context context, Uri uri) {
		if (context == null || uri == null)
			return null;

		Bitmap bitmap;
		try {
			bitmap = BitmapFactory.decodeStream(context.getContentResolver().openInputStream(uri));
		} catch (FileNotFoundException e) {
			e.printStackTrace();
			return null;
		}
		return bitmap;
	}

	/**
	 * 获取到裁剪后图片的uri调用该方法保存到本地
	 * 
	 * @param context
	 *            使用上下文
	 * @param uri
	 * @param fileName
	 * @return
	 */
	public static String saveImg2SD(Context context, Uri uri, String fileName) {
		if (context == null || uri == null)
			return null;
		// 文件夹
		String dir = Environment.getExternalStorageDirectory().getPath() + File.separator + "OSCE" + "/Camera/";

		File file = new File(dir);
		if (!file.exists()) {
			file.mkdirs();
		}
		File f = new File(file, fileName + ".jpg");
		int len;
		FileOutputStream fos = null;
		InputStream is = null;
		byte[] buffer = new byte[1024];
		try {
			f.createNewFile();
			fos = new FileOutputStream(f);
			is = context.getContentResolver().openInputStream(uri);
			while ((len = is.read(buffer)) != -1) {
				fos.write(buffer, 0, len);
			}
			fos.flush();
			fos.close();
			Log.i("***save image file***", f.getAbsolutePath());
			return f.getAbsolutePath();
		} catch (Exception e) {
			e.printStackTrace();
			return null;
		}
	}

	/**
	 * 4.4的版本api返回的uri不兼容，获取图片真正的path
	 */
	@TargetApi(Build.VERSION_CODES.KITKAT)
	public static String getRealPathFromURIKK(Context context, Uri uri) {
		final boolean isKitKat = Build.VERSION.SDK_INT >= Build.VERSION_CODES.KITKAT;
		if (!isKitKat) {
			if (uri.getScheme().equals("file")) {
				return uri.getPath();
			} else {
				return getRealPathFromURI(context, uri);
			}
		}

		// DocumentProvider
		if (isKitKat && DocumentsContract.isDocumentUri(context, uri)) {
			// ExternalStorageProvider
			if (isExternalStorageDocument(uri)) {
				final String docId = DocumentsContract.getDocumentId(uri);
				final String[] split = docId.split(":");
				final String type = split[0];

				if ("primary".equalsIgnoreCase(type)) {
					return Environment.getExternalStorageDirectory() + File.separator + split[1];
				}
			}
			// DownloadsProvider
			else if (isDownloadsDocument(uri)) {

				final String id = DocumentsContract.getDocumentId(uri);
				final Uri contentUri = ContentUris.withAppendedId(Uri.parse("content://downloads/public_downloads"),
						Long.valueOf(id));

				return getDataColumn(context, contentUri, null, null);
			}
			// MediaProvider
			else if (isMediaDocument(uri)) {
				final String docId = DocumentsContract.getDocumentId(uri);
				final String[] split = docId.split(":");
				final String type = split[0];

				Uri contentUri = null;
				if ("image".equals(type)) {
					contentUri = MediaStore.Images.Media.EXTERNAL_CONTENT_URI;
				} else if ("video".equals(type)) {
					contentUri = MediaStore.Video.Media.EXTERNAL_CONTENT_URI;
				} else if ("audio".equals(type)) {
					contentUri = MediaStore.Audio.Media.EXTERNAL_CONTENT_URI;
				}

				final String selection = "_id=?";
				final String[] selectionArgs = new String[] { split[1] };

				return getDataColumn(context, contentUri, selection, selectionArgs);
			}
		}
		// MediaStore (and general)
		else if ("content".equalsIgnoreCase(uri.getScheme())) {

			// Return the remote address
			if (isGooglePhotosUri(uri))
				return uri.getLastPathSegment();

			return getDataColumn(context, uri, null, null);
		}
		// File
		else if ("file".equalsIgnoreCase(uri.getScheme())) {
			return uri.getPath();
		}
		return null;
	}

	private static String getRealPathFromURI(Context context, Uri contentUri) {
		String[] proj = { MediaStore.Images.Media.DATA };
		@SuppressWarnings("deprecation")
		Cursor cursor = ((Activity) context).managedQuery(contentUri, proj, null, null, null);
		int column_index = cursor.getColumnIndexOrThrow(MediaStore.Images.Media.DATA);
		cursor.moveToFirst();
		return cursor.getString(column_index);
	}

	/**
	 * Get the value of the data column for this Uri. This is useful for
	 * MediaStore Uris, and other file-based ContentProviders.
	 *
	 * @param context
	 *            The context.
	 * @param uri
	 *            The Uri to query.
	 * @param selection
	 *            (Optional) Filter used in the query.
	 * @param selectionArgs
	 *            (Optional) Selection arguments used in the query.
	 * @return The value of the _data column, which is typically a file path.
	 */
	public static String getDataColumn(Context context, Uri uri, String selection, String[] selectionArgs) {

		Cursor cursor = null;
		final String column = "_data";
		final String[] projection = { column };

		try {
			cursor = context.getContentResolver().query(uri, projection, selection, selectionArgs, null);
			if (cursor != null && cursor.moveToFirst()) {
				final int index = cursor.getColumnIndexOrThrow(column);
				return cursor.getString(index);
			}
		} finally {
			if (cursor != null)
				cursor.close();
		}
		return null;
	}

	/**
	 * @param uri
	 *            The Uri to check.
	 * @return Whether the Uri authority is ExternalStorageProvider.
	 */
	public static boolean isExternalStorageDocument(Uri uri) {
		return "com.android.externalstorage.documents".equals(uri.getAuthority());
	}

	/**
	 * @param uri
	 *            The Uri to check.
	 * @return Whether the Uri authority is DownloadsProvider.
	 */
	public static boolean isDownloadsDocument(Uri uri) {
		return "com.android.providers.downloads.documents".equals(uri.getAuthority());
	}

	/**
	 * @param uri
	 *            The Uri to check.
	 * @return Whether the Uri authority is MediaProvider.
	 */
	public static boolean isMediaDocument(Uri uri) {
		return "com.android.providers.media.documents".equals(uri.getAuthority());
	}

	/**
	 * @param uri
	 *            The Uri to check.
	 * @return Whether the Uri authority is Google Photos.
	 */
	public static boolean isGooglePhotosUri(Uri uri) {
		return "com.google.android.apps.photos.content".equals(uri.getAuthority());
	}
}
