package com.mx.osce.camera;

import android.graphics.Bitmap;
import android.net.Uri;
import android.os.Environment;

/**
 * 裁剪图片的参数
 *
 * @author Yuli 2015-5-11
 */
public class CropParams {

    public static final int DEFAULT_ASPECT_X = 1;    // 比例
    public static final int DEFAULT_ASPECT_Y = 1;
    public static final int DEFAULT_OUTPUT_X = 400;    // 默认裁剪尺寸
    public static final int DEFAULT_OUTPUT_Y = 400;

    public Uri uri;
    public String type;
    public String outputFormat;
    public String crop;
    public boolean scale;
    public boolean returnData;
    public boolean noFaceDetection;
    public boolean scaleUpIfNeeded;
    public int aspectX;
    public int aspectY;
    public int outputX;
    public int outputY;

    public CropParams() {
        uri = Uri
                .fromFile(Environment.getExternalStorageDirectory())
                .buildUpon()
                .appendPath("tempCropFile.jpg")
                .build();
        type = "image/*";
        outputFormat = Bitmap.CompressFormat.JPEG.toString();
        crop = "true";
        scale = true;
        returnData = false;
        noFaceDetection = true;
        scaleUpIfNeeded = true;
        aspectX = DEFAULT_ASPECT_X;
        aspectY = DEFAULT_ASPECT_Y;
        outputX = DEFAULT_OUTPUT_X;
        outputY = DEFAULT_OUTPUT_Y;
    }
}
