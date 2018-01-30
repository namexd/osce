package com.mx.test.util;

import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.support.v4.util.LruCache;

/**
 * 对图片内存缓存工具类
 * 
 * 
 */
public class ImageLoader {

	/**
	 * 图片缓存的核心类，用于缓存所有下载好的图片，在程序内存达到设定时会将最少最近使用的图片移除掉
	 */
	private static LruCache<String, Bitmap> mMemoryCache;

	/**
	 * ImageLoader的实例
	 */
	private static ImageLoader mImageLoader;

	private ImageLoader() {
		// 获取应用程序最大可用内存
		int maxMemory = (int) Runtime.getRuntime().maxMemory();
		int cacheSize = maxMemory / 8;
		// 设置图片缓存大小为程序最大可用内存的1/8
		mMemoryCache = new LruCache<String, Bitmap>(cacheSize) {
			@Override
			protected int sizeOf(String key, Bitmap bitmap) {
				return bitmap.getRowBytes() * bitmap.getHeight();
			}
		};
	}

	/**
	 * 获取ImageLoader的实例
	 * 
	 * @return ImageLoader的实例
	 */
	public static ImageLoader getInstance() {
		if (mImageLoader == null) {
			mImageLoader = new ImageLoader();
		}
		return mImageLoader;
	}

	/**
	 * 将一张图片存储到LruCache中
	 * 
	 * @param key
	 *            LruCache的键，这里传入图片的URL地址
	 * @param bitmap
	 *            LruCache的键，这里传入从网络上下载的Bitmap对象
	 */
	public void addBitmapToMemoryCache(String key, Bitmap bitmap) {
		if (getBitmapFromMemoryCache(key) == null) {
			mMemoryCache.put(key, bitmap);
		}
	}

	/**
	 * 从LruCache中获取一张图片，如果不存在就返回null
	 * 
	 * @param key
	 *            LruCache的键，这里传入图片的URL地址
	 * @return 对应传入键的Bitmap对象，或者null
	 */
	public Bitmap getBitmapFromMemoryCache(String key) {
		return mMemoryCache.get(key);
	}

	public static int calculateInSampleSize(BitmapFactory.Options options, int reqWidth) {
		// 源图片的宽度
		final int width = options.outWidth;
		int inSampleSize = 1;
		if (width > reqWidth) {
			// 计算出实际宽度和目标宽度的比
			final int widthRatio = (int) Math.ceil((float) width / (float) reqWidth);
			inSampleSize = widthRatio;
		}
		return inSampleSize;
	}

	public static int calculateInSampleSize(BitmapFactory.Options options, int reqWidth, int reqHeight) {
		// 源图片的宽度
		final int width = options.outWidth;
		final int height = options.outHeight;
		int inSampleSize = 1;
		if (width > reqWidth) {
			// 计算出实际宽度和目标宽度的比
			final int widthRatio = Math.round((float) width / (float) reqWidth);
			inSampleSize = widthRatio;
		}
		if (height > reqHeight) {
			final int heightRatio = Math.round((float) height / (float) reqHeight);
			if (inSampleSize < heightRatio)
				inSampleSize = heightRatio;
		}
		return inSampleSize;
	}

	public static Bitmap decodeSampledBitmapFromResource(String pathName, int reqWidth) {
		// 第一次解析将inJustDecodeBounds设置为true，来获取图片大小
		final BitmapFactory.Options options = new BitmapFactory.Options();
		options.inJustDecodeBounds = true;
		BitmapFactory.decodeFile(pathName, options);
		// 调用上面定义的方法计算inSampleSize
		options.inSampleSize = calculateInSampleSize(options, reqWidth);
		// 使用获取到的inSampleSize值再次解析图
		options.inJustDecodeBounds = false;
		return BitmapFactory.decodeFile(pathName, options);
	}

	public static Bitmap decodeSampledBitmapFromResource(String pathName, int reqWidth, int reqHeight) {
		// 第一次解析将inJustDecodeBounds设置为true，来获取图片大小
		final BitmapFactory.Options options = new BitmapFactory.Options();
		options.inJustDecodeBounds = true;
		BitmapFactory.decodeFile(pathName, options);
		// options.inSampleSize = calculateInSampleSize(options, reqWidth,
		// reqHeight);
		options.outWidth = reqWidth;
		options.outHeight = reqHeight;
		// 使用获取到的inSampleSize值再次解析图
		options.inJustDecodeBounds = false;
		return BitmapFactory.decodeFile(pathName, options);
	}

}
