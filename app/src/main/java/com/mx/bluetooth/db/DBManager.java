package com.mx.bluetooth.db;

import java.util.ArrayList;
import java.util.List;

import com.mx.bluetooth.bean.UploadScoreBean;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;

public class DBManager {  
    private DBHelper helper;  
    private SQLiteDatabase db;  
      
    public DBManager(Context context) {  
        helper = new DBHelper(context);  
        //因为getWritableDatabase内部调用了mContext.openOrCreateDatabase(mName, 0, mFactory);  
        //所以要确保context已初始化,我们可以把实例化DBManager的步骤放在Activity的onCreate里  
        db = helper.getWritableDatabase(); 
        
    }  
      
    /** 
     * add Score 
     * @param Scores 
     */  
    public void add(List<UploadScoreBean> Scores) {  
        db.beginTransaction();  //开始事务  
        try {  
            for (UploadScoreBean uploadScoreBean : Scores) {  
                db.execSQL("INSERT INTO score VALUES(null, ?, ?, ?,?,?,?,?,?,?,?,?,?,?,?,?)", new Object[]{uploadScoreBean.getStudent_id(), uploadScoreBean.getStation_id(),
                		                                                            uploadScoreBean.getTeacher_id(),uploadScoreBean.getBegin_dt(),
                		                                                            uploadScoreBean.getEnd_dt(),uploadScoreBean.getEvaluate(),
                		                                                            uploadScoreBean.getPatient(),uploadScoreBean.getAffinity(),
                		                                                            uploadScoreBean.getOperation(), uploadScoreBean.getSkilled(),
                		                                                            uploadScoreBean.getStates(),uploadScoreBean.getExam_screening_id(),
                		                                                            uploadScoreBean.getUpload_image_return(),uploadScoreBean.getScore(),
                		                                                            uploadScoreBean.getUrl()});  
            }  
            db.setTransactionSuccessful();  //设置事务成功完成  
        } finally {  
            db.endTransaction();    //结束事务  
        }  
    }  
    /** 
     * add Score 
     * @param Score 
     */  
    public boolean add(UploadScoreBean Score) {  
        db.beginTransaction();  //开始事务  
        try {  
            
                db.execSQL("INSERT INTO score VALUES(null, ?, ?, ?,?,?,?,?,?,?,?,?,?,?,?,?)", 
                		new Object[]{
                				Score.getStudent_id(), Score.getStation_id(),
                				Score.getTeacher_id(),Score.getBegin_dt(),
                				Score.getEnd_dt(),Score.getEvaluate(),
                				Score.getPatient(),Score.getAffinity(),
                				Score.getOperation(), Score.getSkilled(),
                				Score.getStates(),Score.getExam_screening_id(),
                				Score.getUpload_image_return(),Score.getScore(),
                				Score.getUrl()
                		});  
              
            db.setTransactionSuccessful();  //设置事务成功完成  
           
        } catch (SQLException e){
        	 return false; 
        }finally {  
            db.endTransaction();  //结束事务  
        }
        return true;
    }  
      
    /** 
     * update States  
     * @param Score 
     */  
    public void updateStates(UploadScoreBean Score) {  
        ContentValues cv = new ContentValues();  
        cv.put("States", Score.getStates());  
        db.update("score", cv, "student_id = ? and station_id=? and teacher_id=?", new String[]{Score.getStudent_id(),Score.getStation_id(),Score.getTeacher_id()});  
    }  
      
    /** 
     * delete States  
     * @param Score 
     */ 
    public void deleteScore(UploadScoreBean Score) {  
        db.delete("score", "student_id = ? and station_id=? and teacher_id=?", new String[]{Score.getStudent_id(),Score.getStation_id(),Score.getTeacher_id()});  
    }  
      
    /** 
     * query  Scores, return list 
     * @return List<UploadScoreBean> 
     */  
    public ArrayList<UploadScoreBean> query(String states) {  
        ArrayList<UploadScoreBean> Scores = new ArrayList<UploadScoreBean>();  
        Cursor c = queryTheCursor(states);  
        while (c.moveToNext()) {  
        	UploadScoreBean Score = new UploadScoreBean();
        	Score.setStation_id(c.getString(c.getColumnIndex("station_id")));
        	Score.setStudent_id(c.getString(c.getColumnIndex("student_id")));
        	Score.setTeacher_id(c.getString(c.getColumnIndex("teacher_id")));
        	Score.setEvaluate("");
        	Score.setAffinity(c.getString(c.getColumnIndex("affinity")));
        	Score.setBegin_dt(c.getString(c.getColumnIndex("begin_dt")));
        	Score.setEnd_dt(c.getString(c.getColumnIndex("end_dt")));
        	Score.setExam_screening_id(c.getString(c.getColumnIndex("exam_screening_id")));
        	Score.setOperation("");
        	Score.setSkilled("");
        	Score.setStates(c.getString(c.getColumnIndex("states")));
        	Score.setUpload_image_return(c.getString(c.getColumnIndex("upload_image_return")));
        	Score.setScore(c.getString(c.getColumnIndex("score")));
        	Score.setPatient(c.getString(c.getColumnIndex("patient")));
        	Score.setUrl(c.getString(c.getColumnIndex("url")));
        	Scores.add(Score);  
        }  
        c.close();  
        return Scores;  
    }  
      
    /** 
     * query all Scores, return cursor 
     * @return  Cursor 
     */  
    public Cursor queryTheCursor(String states) {  
        Cursor c = db.rawQuery("SELECT * FROM score where states=?",new String[]{states} );  
        return c;  
    }
    /** 
     * clean data 
     * @return  Cursor 
     */  
    public void clean() {  
    	db.execSQL("DELETE  FROM score");
       
    }  
      
    /** 
     * close database 
     */  
    public void closeDB() {  
        db.close();  
    }  
}  