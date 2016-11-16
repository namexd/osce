package com.mx.osce.util;

import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Environment;
import android.widget.ImageView;

/**
 * 异步下载图片的任务。
 * 
 * @author
 */
public class LoadImageTask extends AsyncTask<String, Void, Bitmap> {

	/**
	 * 图片的URL地址
	 */
	private String mImageUrl;
	private static ImageLoader mImageLoader;
	private int mColumnWidth;
	/**
	 * 可重复使用的ImageView
	 */
	private ImageView mImageView;
	private static Context mContext;

	public LoadImageTask() {
	}

	/**
	 * 将可重复使用的ImageView传入
	 * 
	 * @param imageView
	 */
	public LoadImageTask(Context context, ImageView imageView, int columnWidth) {
		mImageView = imageView;
		mImageLoader = ImageLoader.getInstance();
		mColumnWidth = columnWidth;
		mContext = context;
	}

	@Override
	protected void onPreExecute() {
		super.onPreExecute();
		// mImageView.setBackgroundColor(Color.WHITE);
		if (mColumnWidth > 0) {
			mImageView.setMinimumHeight(mColumnWidth);
			mImageView.setMaxHeight(mColumnWidth);
			mImageView.setMinimumWidth(mColumnWidth);
			mImageView.setMaxWidth(mColumnWidth);
		}
		// mImageView.setBackgroundDrawable(mContext.getResources().getDrawable(R.drawable.empty_background));
	}

	@Override
	protected Bitmap doInBackground(String... params) {
		mImageUrl = params[0];
		Bitmap imageBitmap = mImageLoader.getBitmapFromMemoryCache(mImageUrl);
		if (imageBitmap == null) {
			imageBitmap = loadImage(mImageUrl, mContext);
		}
		return imageBitmap;
	}

	@Override
	protected void onPostExecute(Bitmap bitmap) {
		if (bitmap != null) {

			// 防止图片闪烁
			if (mImageUrl.equals((String) mImageView.getTag())) {
				mImageView.setImageBitmap(bitmap);
				mImageView.setBackgroundColor(Color.TRANSPARENT);
			}

		}
	}

	/**
	 * 根据传入的URL，对图片进行加载。如果这张图片已经存在于SD卡中，则直接从SD卡里读取，否则就从网络上下载。
	 * 
	 * @param imageUrl
	 *            图片的URL地址
	 * @return 加载到内存的图片。
	 */
	private Bitmap loadImage(String imageUrl, Context context) {
		File imageFile = new File(getImagePath(imageUrl, mContext));
		if (!imageFile.exists()) {
			downloadImage(imageUrl, mColumnWidth);
		}
		if (imageUrl != null) {
			Bitmap bitmap = ImageLoader.decodeSampledBitmapFromResource(imageFile.getPath(), mColumnWidth);
			if (bitmap != null) {
				mImageLoader.addBitmapToMemoryCache(imageUrl, bitmap);
				return bitmap;
			}
		}
		return null;
	}

	/**
	 * 将图片下载到SD卡缓存起来。
	 * 
	 * @param imageUrl
	 *            图片的URL地址。
	 */
	public static void downloadImage(String imageUrl, int columnWidth) {

		FileOutputStream fos = null;
		BufferedOutputStream bos = null;
		BufferedInputStream bis = null;
		File imageFile = null;
		try {

			bis = new BufferedInputStream(HttpImg.connectImgUrl(imageUrl));
			imageFile = new File(getImagePath(imageUrl, mContext));
			fos = new FileOutputStream(imageFile);
			bos = new BufferedOutputStream(fos);
			byte[] b = new byte[1024];
			int length;
			while ((length = bis.read(b)) != -1) {
				bos.write(b, 0, length);
				bos.flush();
			}
		} catch (Exception e) {
			e.printStackTrace();
		} finally {
			try {
				if (bis != null) {
					bis.close();
				}
				if (bos != null) {
					bos.close();
				}
			} catch (IOException e) {
				e.printStackTrace();
			}
		}
		if (imageFile != null) {
			Bitmap bitmap = ImageLoader.decodeSampledBitmapFromResource(imageFile.getPath(), columnWidth);
			if (bitmap != null) {
				mImageLoader.addBitmapToMemoryCache(imageUrl, bitmap);
			}
		}
	}

	/**
	 * 获取图片的本地存储路径。
	 * 
	 * @param imageUrl
	 *            图片的URL地址。
	 * @return 图片的本地存储路径。
	 */
	public static String getImagePath(String imageUrl, Context context) {
		int lastSlashIndex = imageUrl.lastIndexOf("/");
		String imageName = imageUrl.substring(lastSlashIndex + 1);
		String imageDir = Environment.getExternalStorageDirectory().toString() + File.separator
				+ context.getPackageName() + File.separator + "image/";
		File file = new File(imageDir);
		if (!file.exists()) {
			file.mkdirs();
		}
		// String imagePath = imageDir + AbMd5.MD5(imageName);
		String imagePath = imageDir + imageName;
		return imagePath;
	}
}
