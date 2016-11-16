package com.mx.osce.util;

import android.app.ActivityManager;
import android.content.Context;
import android.util.Log;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.toolbox.ImageLoader;
import com.android.volley.toolbox.Volley;

/**
 * Created by storm on 14-3-25.
 */
public class RequestManager {
	private static RequestQueue mRequestQueue;
	private static ImageLoader mImageLoader;

	public RequestManager(Context context) {
		init(context);
	}

	public static void init(Context context) {
		if (mRequestQueue == null) {
			mRequestQueue = Volley.newRequestQueue(context);

			Out.out(mRequestQueue.toString());
			int memClass = ((ActivityManager) context.getSystemService(Context.ACTIVITY_SERVICE)).getMemoryClass();
			// Use 1/8th of the available memory for this memory cache.
			int cacheSize = 1024 * 1024 * memClass / 8;
			mImageLoader = new ImageLoader(mRequestQueue, new BitmapLruCache(cacheSize));
		}
	}

	public static RequestQueue getRequestQueue() {
		if (mRequestQueue != null) {

			return mRequestQueue;
		} else {
			throw new IllegalStateException("RequestQueue not initialized");
		}
	}

	public static void addRequest(Request<?> request, Object tag) {
		if (tag != null) {
			request.setTag(tag);
		}

		if (mRequestQueue == null) {
		}
		mRequestQueue.add(request);
		//
		Log.i("request--url", request.getUrl());
	}

	public static void cancelAll(Object tag) {
		mRequestQueue.cancelAll(tag);
	}

	/**
	 * Returns instance of ImageLoader initialized with {@see FakeImageCache}
	 * which effectively means that no memory caching is used. This is useful
	 * for images that you know that will be show only once.
	 * 
	 * @return
	 */
	public static ImageLoader getImageLoader() {
		if (mImageLoader != null) {
			return mImageLoader;
		} else {
			throw new IllegalStateException("ImageLoader not initialized");
		}
	}
}
