package com.mx.test.util;

import java.io.ByteArrayOutputStream;
import java.io.FileOutputStream;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.BitmapDrawable;
import android.graphics.drawable.Drawable;

public class BitmapUtils {

	public BitmapUtils(Context context, String path) {
		// TODO Auto-generated constructor stub
	}

	public static Bitmap Bytes2Bimap(byte[] paramArrayOfByte) {
		if (paramArrayOfByte.length != 0) {
			return BitmapFactory.decodeByteArray(paramArrayOfByte, 0,
					paramArrayOfByte.length);
		}
		return null;
	}

	public static byte[] bitmapToByte(Bitmap paramBitmap) {
		if (paramBitmap == null) {
			return null;
		}
		ByteArrayOutputStream localByteArrayOutputStream = new ByteArrayOutputStream();
		paramBitmap.compress(Bitmap.CompressFormat.PNG, 100,
				localByteArrayOutputStream);
		return localByteArrayOutputStream.toByteArray();
	}

	public static Drawable bitmapToDrawable(Bitmap paramBitmap) {
		if (paramBitmap == null) {
			return null;
		}
		return new BitmapDrawable(paramBitmap);
	}

	public static Bitmap getBmpFromFile(Context paramContext, String paramString) {
		try {
			Bitmap localBitmap = BitmapFactory.decodeStream(paramContext
					.openFileInput(paramString));
			return localBitmap;
		} catch (Exception localException) {
			localException.printStackTrace();
			return null;
		} 
	}

	public static void saveBmpToPng(Context paramContext, Bitmap paramBitmap,
			String paramString) {
		try {
			String str = paramString + ".png";
			
			FileOutputStream localFileOutputStream = paramContext
					.openFileOutput(str, 0);
			paramBitmap.compress(Bitmap.CompressFormat.PNG, 100,
					localFileOutputStream);
			localFileOutputStream.flush();
			localFileOutputStream.close();
			return;
		} catch (Exception localException) {
			localException.printStackTrace();
			return;
		} catch (Error localError) {
			localError.printStackTrace();
		}
	}

}
