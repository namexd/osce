package com.mx.osce.util;

import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Build;
import android.os.Environment;
import android.util.DisplayMetrics;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;
import android.widget.ListAdapter;
import android.widget.ListView;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.UnsupportedEncodingException;
import java.math.BigDecimal;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.text.DecimalFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import com.lidroid.xutils.BitmapUtils;
import com.lidroid.xutils.DbUtils;


public class CommonTool {

    public static BitmapUtils mBitmapUtils;

    /**
     * 图片保存位置
     */
    private static String PATH = "/jazdd/image/";
    /**
     * 下载保存位置
     */
    private static String DOWNLOAD_PATH = "/jzdd/download/";

    private static SimpleDateFormat sdf = new SimpleDateFormat("yyyy年MM月dd日");
    private static SimpleDateFormat sdf3 = new SimpleDateFormat("yyyy年MM月dd日 HH点mm分");
    private static SimpleDateFormat sdf2 = new SimpleDateFormat("MM-dd HH:mm");

    private static DecimalFormat format = new DecimalFormat("0.00");

    /**
     * @param context
     * @param token   hide softInputMethod
     */
    public static void hideSoftInputMethod(Context context, View token) {
        InputMethodManager manager = (InputMethodManager) context.getSystemService(Context.INPUT_METHOD_SERVICE);
        manager.hideSoftInputFromWindow(token.getWindowToken(), InputMethodManager.HIDE_NOT_ALWAYS);
    }

    public static BitmapUtils getBitmapUtils(Context context) {
        String path = getDiskPath();
        if (mBitmapUtils == null) {
            mBitmapUtils = new BitmapUtils(context, path);
        }
        return mBitmapUtils;
    }

    /**
     * 得到数据库DB
     */
    public static DbUtils getDbUtils(Context context) {
        DbUtils db = DbUtils.create(context, "luoye", 1, new DbUtils.DbUpgradeListener() {
            @Override
            public void onUpgrade(DbUtils dbUtils, int oldVersion, int newVersion) {
//                try {
//                    Log.d("CommontTool", "upgrade db---->oldVersion---new Version---" + oldVersion + "---" + newVersion);
//                    dbUtils.execNonQuery("ALTER TABLE Notification ADD COLUMN isDelete DEFAULT 0 NOT NULL");
//                } catch (DbException e) {
//                    e.printStackTrace();
//                }
            }
        });
        db.configAllowTransaction(true);
        return db;
    }
    /**
     * 图片压缩.
     */
    public static ByteArrayOutputStream comp1(Bitmap image, int with, int height) {
        ByteArrayOutputStream baos = new ByteArrayOutputStream();
        image.compress(Bitmap.CompressFormat.JPEG, 100, baos);
        //判断如果图片大于1M,进行压缩避免在生成图片（BitmapFactory.decodeStream）时溢出
        if (baos.toByteArray().length / 1024 > 1024) {
            //重置baos.清空baos
            baos.reset();
            //这里压缩80%，把压缩后的数据存放到baos中
            image.compress(Bitmap.CompressFormat.JPEG, 80, baos);
        }
        ByteArrayInputStream isBm = new ByteArrayInputStream(baos.toByteArray());
        BitmapFactory.Options newOpts = new BitmapFactory.Options();
        //开始读入图片，此时把options.inJustDecodeBounds 设回true了
        newOpts.inJustDecodeBounds = true;
        Bitmap bitmap = BitmapFactory.decodeStream(isBm, null, newOpts);
        newOpts.inJustDecodeBounds = false;
        int w = newOpts.outWidth;
        int h = newOpts.outHeight;
        //现在主流手机比较多是800*480分辨率，所以高和宽我们设置为
        float hh = height;//这里设置高度为800f
        float ww = with;//这里设置宽度为480f
        //缩放比。由于是固定比例缩放，只用高或者宽其中一个数据进行计算即可
        int be = 1;//be=1表示不缩放
        if (w > h && w > ww) {//如果宽度大的话根据宽度固定大小缩放
            be = (int) (newOpts.outWidth / ww);
        } else if (w < h && h > hh) {//如果高度高的话根据宽度固定大小缩放
            be = (int) (newOpts.outHeight / hh);
        }
        if (be <= 0)
            be = 1;
        newOpts.inSampleSize = be;//设置缩放比例
        //重新读入图片，注意此时已经把options.inJustDecodeBounds 设回false了
        isBm = new ByteArrayInputStream(baos.toByteArray());
        bitmap = BitmapFactory.decodeStream(isBm, null, newOpts);
        return compressImage1(bitmap);//压缩好比例大小后再进行质量压缩
    }
    private static ByteArrayOutputStream compressImage1(Bitmap image) {
        ByteArrayOutputStream baos = new ByteArrayOutputStream();
        //质量压缩方法，这里100表示不压缩，把压缩后的数据存放到baos中
        image.compress(Bitmap.CompressFormat.JPEG, 100, baos);
        int options = 100;
        //循环判断如果压缩后图片是否大于100kb,大于继续压缩
        while (baos.toByteArray().length / 1024 > 200) {
            baos.reset();//重置baos即清空baos
            options -= 10;//每次都减少10
            //这里压缩options%，把压缩后的数据存放到baos中
            image.compress(Bitmap.CompressFormat.JPEG, options, baos);
        }
        return baos;//把压缩后的数据baos存放到ByteArrayInputStream中
        //Bitmap bitmap = BitmapFactory.decodeStream(isBm, null, null);//把ByteArrayInputStream数据生成图片
        //return bitmap;
    }
//    /**
//     * 得到自定义的progressDialog,用于等待使用
//     *
//     * @param context
//     * @param msg
//     * @return
//     */
//    public static Dialog createLoadingDialog(Context context, String msg) {
//        LayoutInflater inflater = LayoutInflater.from(context);
//        View v = inflater.inflate(R.layout.loading_dialog, null);// 得到加载view
//        RelativeLayout layout = (RelativeLayout) v
//                .findViewById(R.id.dialog_view);// 加载布局
//        // main.xml中的ImageView
//        ImageView spaceshipImage = (ImageView) v.findViewById(R.id.img);
//        TextView tipTextView = (TextView) v.findViewById(R.id.tipTextView);// 提示文字
//        // 加载动画
//        Animation hyperspaceJumpAnimation = AnimationUtils.loadAnimation(
//                context, R.anim.load_animation);
//        // 使用ImageView显示动画
//        spaceshipImage.startAnimation(hyperspaceJumpAnimation);
//        tipTextView.setText(msg);// 设置加载信息
//
//        // 创建自定义样式dialog
//        Dialog loadingDialog = new Dialog(context, R.style.loading_dialog);
//        loadingDialog.setCancelable(false);// 不可以用“返回键”取消
//        loadingDialog.setContentView(layout);// 设置布局
//        return loadingDialog;
//    }
//
//    private static Toast toast;
//
//    /**
//     * 错误提示样式.Toast(short)
//     *
//     * @param context
//     * @param msg
//     */
//    public static void errorToastToShow(Context context, String msg) {
//        LayoutInflater inflater = LayoutInflater.from(context);
//        View v = inflater.inflate(R.layout.loading_dialog, null);// 得到加载view
//        RelativeLayout layout = (RelativeLayout) v
//                .findViewById(R.id.dialog_view);// 加载布局
//        // main.xml中的ImageView
//        ImageView spaceshipImage = (ImageView) v.findViewById(R.id.img);
//        TextView tipTextView = (TextView) v.findViewById(R.id.tipTextView);// 提示文字
//        spaceshipImage.setVisibility(View.GONE);
//        tipTextView.setText(msg);
//        if (toast == null) {
//            toast = new Toast(context);
//            toast.setGravity(Gravity.TOP, 0, 0);
//            toast.setDuration(Toast.LENGTH_SHORT);
//            toast.setView(layout);
//        } else {
//            toast.setView(layout);
//        }
//
//        toast.show();
//    }

    /**
     * 创建图片缓存路径
     */
    private static String getDiskPath() {
        // 判断sd卡是否存在
        boolean sdCardExist = Environment.getExternalStorageState().equals(
                Environment.MEDIA_MOUNTED);
        // 创建目录
        if (sdCardExist) {
            File dir = Environment.getExternalStorageDirectory();
            String path = dir.getPath();
            File file = new File(path + PATH);
            if (!file.exists()) {
                file.mkdir();
            }
            return file.getPath();
        }
        return null;
    }

    /**
     * 创建下载路径
     */
    public static String getDiskApkPath() {
        // 判断sd卡是否存在
        boolean sdCardExist = Environment.getExternalStorageState().equals(
                Environment.MEDIA_MOUNTED);
        // 创建目录
        if (sdCardExist) {
            File dir = Environment.getExternalStorageDirectory();
            String path = dir.getPath();
            File file = new File(path + DOWNLOAD_PATH);
            if (!file.exists()) {
                file.mkdir();
            }
            return file.getPath();
        }
        return null;
    }

    /**
     * 检测当的网络（WLAN、3G/2G）状态
     *
     * @param context Context
     * @return true 表示网络可用
     */
    public static boolean isNetworkAvailable(Context context) {
        ConnectivityManager connectivity = (ConnectivityManager) context.getSystemService(Context.CONNECTIVITY_SERVICE);
        if (connectivity != null) {
            NetworkInfo info = connectivity.getActiveNetworkInfo();
            if ((info != null) && info.isConnected()) {
                // 当前网络是连接的
                if (info.getState() == NetworkInfo.State.CONNECTED) {
                    // 当前所连接的网络可用
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 转换时间戳为String
     *
     * @param data
     */
    public static String formatDateNotYear(String data) {
        if (data != null) {
            if (data.length() == 10) {
                String date = sdf2.format(new Date(Long.parseLong(data) * 1000L));
                return date;
            } else {
                return data;
            }
        } else {
            return "";
        }
    }

    /**
     * 转换时间戳为String
     *
     * @param data
     * @return
     */
    public static String formatDateNotHour(String data) {
        if (data != null) {
            if (data.length() == 10) {
                String date = sdf.format(new Date(Long.parseLong(data) * 1000L));
                return date;
            } else {
                return data;
            }
        } else {
            return "";
        }
    }

    public static long parseDate(String date) {
        try {
            return sdf.parse(date).getTime();
        } catch (ParseException e) {
            e.printStackTrace();
        }
        return 0;
    }

    /**
     * 转换时间戳为String,XXXX年XX月XX日 XX点XX分
     *
     * @param data
     * @return
     */
    public static String formatDateAll(String data) {
        if (data != null) {
            if (data.length() == 10) {
                String date = sdf3.format(new Date(Long.parseLong(data) * 1000L));
                return date;
            } else {
                return data;
            }
        } else {
            return "";
        }
    }

    /**
     * 转换时间戳为date
     *
     * @param data
     * @return
     */
    public static Date reservationsData2(String data) {
        if (data != null) {
            if (data.length() == 10) {
                return new Date(Long.parseLong(data) * 1000L);
            } else {
                return new Date(Long.parseLong(data) * 1000L);
            }
        } else {
            return null;
        }
    }

    /**
     * 转换时间戳为date
     *
     * @param data
     * @return
     */
    public static int StringToMonth(String data) {
        Date date = reservationsData2(data);
        Calendar calendar = Calendar.getInstance();
        calendar.setTime(date);
        return calendar.get(Calendar.MONTH) + 1;
    }

    public static int getWidth(Context context) {
        DisplayMetrics displayMetrics = new DisplayMetrics();
        WindowManager windowManager = (WindowManager) context.getSystemService(Context.WINDOW_SERVICE);
        windowManager.getDefaultDisplay().getMetrics(displayMetrics);
        return displayMetrics.widthPixels;
    }

    public static int getHeight(Context context) {
        DisplayMetrics displayMetrics = new DisplayMetrics();
        WindowManager windowManager = (WindowManager) context.getSystemService(Context.WINDOW_SERVICE);
        windowManager.getDefaultDisplay().getMetrics(displayMetrics);
        return displayMetrics.heightPixels;

    }

//    public static TransitionDrawable getTransitionDrawable(Context context) {
//
//        Drawable[] drawables = {new ColorDrawable(context.getResources().getColor(R.color.hexin_transparent)),
//            new ColorDrawable(context.getResources().getColor(R.color.commom_half_transparent))};
//        return new TransitionDrawable(drawables);
//    }

    /**
     * MD5加密返回32位字符串
     */
    public static String getMD5FromStr(String str) {
        MessageDigest messageDigest = null;

        try {
            messageDigest = MessageDigest.getInstance("MD5");
            messageDigest.reset();
            messageDigest.update(str.getBytes("UTF-8"));
        } catch (NoSuchAlgorithmException e) {
        } catch (UnsupportedEncodingException e) {
        }

        byte[] byteArray = messageDigest.digest();
        StringBuffer md5StrBuff = new StringBuffer();

        for (int i = 0; i < byteArray.length; i++) {
            if (Integer.toHexString(0xFF & byteArray[i]).length() == 1)
                md5StrBuff.append("0").append(
                        Integer.toHexString(0xFF & byteArray[i]));
            else
                md5StrBuff.append(Integer.toHexString(0xFF & byteArray[i]));
        }

        return md5StrBuff.toString();
    }


    /**
     * 安装 APK
     *
     * @param paramContext
     * @param paramString  下载位置文件
     */
    public static void installApk(Context paramContext, String paramString) {
        Intent localIntent = new Intent("android.intent.action.VIEW");
        if (Build.VERSION.SDK_INT > 15) {
            localIntent.addFlags(Intent.FLAG_RECEIVER_FOREGROUND);
        }

        localIntent.setDataAndType(Uri.fromFile(new File(paramString)), "application/vnd.android.package-archive");
        paramContext.startActivity(localIntent);
    }

    /**
     * 图片压缩.
     */
    public static ByteArrayInputStream comp(Bitmap image) {
        ByteArrayOutputStream baos = new ByteArrayOutputStream();
        image.compress(Bitmap.CompressFormat.JPEG, 100, baos);
        //判断如果图片大于1M,进行压缩避免在生成图片（BitmapFactory.decodeStream）时溢出
        if (baos.toByteArray().length / 1024 > 1024) {
            //重置baos.清空baos
            baos.reset();
            //这里压缩80%，把压缩后的数据存放到baos中
            image.compress(Bitmap.CompressFormat.JPEG, 80, baos);
        }
        ByteArrayInputStream isBm = new ByteArrayInputStream(baos.toByteArray());
        BitmapFactory.Options newOpts = new BitmapFactory.Options();
        //开始读入图片，此时把options.inJustDecodeBounds 设回true了
        newOpts.inJustDecodeBounds = true;
        Bitmap bitmap = BitmapFactory.decodeStream(isBm, null, newOpts);
        newOpts.inJustDecodeBounds = false;
        int w = newOpts.outWidth;
        int h = newOpts.outHeight;
        //现在主流手机比较多是800*480分辨率，所以高和宽我们设置为
        float hh = 800f;//这里设置高度为800f
        float ww = 480f;//这里设置宽度为480f
        //缩放比。由于是固定比例缩放，只用高或者宽其中一个数据进行计算即可
        int be = 1;//be=1表示不缩放
        if (w > h && w > ww) {//如果宽度大的话根据宽度固定大小缩放
            be = (int) (newOpts.outWidth / ww);
        } else if (w < h && h > hh) {//如果高度高的话根据宽度固定大小缩放
            be = (int) (newOpts.outHeight / hh);
        }
        if (be <= 0)
            be = 1;
        newOpts.inSampleSize = be;//设置缩放比例
        //重新读入图片，注意此时已经把options.inJustDecodeBounds 设回false了
        isBm = new ByteArrayInputStream(baos.toByteArray());
        bitmap = BitmapFactory.decodeStream(isBm, null, newOpts);
        return compressImage(bitmap);//压缩好比例大小后再进行质量压缩
    }

    /**
     * 图片压缩
     */
    private static ByteArrayInputStream compressImage(Bitmap image) {
        ByteArrayOutputStream baos = new ByteArrayOutputStream();
        //质量压缩方法，这里100表示不压缩，把压缩后的数据存放到baos中
        image.compress(Bitmap.CompressFormat.JPEG, 100, baos);
        int options = 100;
        //循环判断如果压缩后图片是否大于100kb,大于继续压缩
        while (baos.toByteArray().length / 1024 > 200) {
            baos.reset();//重置baos即清空baos
            options -= 10;//每次都减少10
            //这里压缩options%，把压缩后的数据存放到baos中
            image.compress(Bitmap.CompressFormat.JPEG, options, baos);
        }
        return new ByteArrayInputStream(baos.toByteArray());//把压缩后的数据baos存放到ByteArrayInputStream中
        //Bitmap bitmap = BitmapFactory.decodeStream(isBm, null, null);//把ByteArrayInputStream数据生成图片
        //return bitmap;
    }

    /**
     * float四舍五入,保留小数点两位.
     *
     * @param num 需要转换的数据
     */
    public static String reservationsTwo(float num) {

        if (format == null) {
            format = new DecimalFormat("0.00");
        }
        if (num != 0) {
            return format.format(new BigDecimal(num));
        } else {
            return "0";
        }

    }
    

	
	public static void setListViewHeightBasedOnChildren(ListView listView) {
	    if(listView == null) return;
	    ListAdapter listAdapter = listView.getAdapter();
	    if (listAdapter == null) {
	        // pre-condition
	        return;
	    }
	    int totalHeight = 0;
	    for (int i = 0; i < listAdapter.getCount(); i++) {
	        View listItem = listAdapter.getView(i, null, listView);
	        listItem.measure(0, 0);
	        totalHeight += listItem.getMeasuredHeight();
	    }
	    ViewGroup.LayoutParams params = listView.getLayoutParams();
	    params.height = totalHeight + (listView.getDividerHeight() * (listAdapter.getCount() - 1));
	    listView.setLayoutParams(params);
	}
}
