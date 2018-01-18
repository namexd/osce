package com.mx.osce.db;

import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.util.Log;

public class DBHelper extends SQLiteOpenHelper {  
	  
    private static final String DATABASE_NAME = "score.db";  
    private static final int DATABASE_VERSION = 1;  
      
    public DBHelper(Context context) {  
        //CursorFactory设置为null,使用默认值  
        super(context, DATABASE_NAME, null, DATABASE_VERSION);  
    }  
  
    //数据库第一次被创建时onCreate会被调用  
    @Override  
    public void onCreate(SQLiteDatabase db) { 
    	Log.v("DBHelper", "onCreate");
        db.execSQL("CREATE TABLE IF NOT EXISTS score" +  
                "(_id INTEGER PRIMARY KEY AUTOINCREMENT, student_id VARCHAR, station_id VARCHAR, teacher_id VARCHAR,begin_dt VARCHAR, end_dt VARCHAR, evaluate VARCHAR, patient VARCHAR, affinity VARCHAR,operation VARCHAR,skilled VARCHAR,states VARCHAR,exam_screening_id VARCHAR,upload_image_return VARCHAR,score VARCHAR,url VARCHAR)");  
    }  
  
    //如果DATABASE_VERSION值被改为2,系统发现现有数据库版本不同,即会调用onUpgrade  
    @Override  
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {  
        db.execSQL("ALTER TABLE score ADD COLUMN other STRING");  
    }  
}  
