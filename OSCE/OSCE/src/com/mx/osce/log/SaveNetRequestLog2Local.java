package com.mx.osce.log;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStreamWriter;
import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import org.json.JSONException;
import org.json.JSONObject;

import com.google.gson.Gson;
import com.google.gson.JsonSyntaxException;
import com.google.gson.reflect.TypeToken;
import com.mx.osce.BaseActivity;
import com.mx.osce.bean.GradePointBean_Net;
import com.mx.osce.save.FilePathUtil;
import com.mx.osce.util.Utils;

import android.content.Context;
import android.support.v4.view.animation.FastOutLinearInInterpolator;
import android.util.Log;

public class SaveNetRequestLog2Local {

	public static final String ROOT_DIR = File.separator + "sdcard" + File.separator + "com.mx.osce" + File.separator
			+ "NetRequestLog/";

	public static void SavNetLogLocal(String url, Map<String, String> params, String content) {
		// 一个登陆用户一个文件
		String subSaveDir = ROOT_DIR + Utils.getSharedPrefrences(BaseActivity.mScontext, "user_phone") + File.separator;
		File subSaveDirFile = new File(subSaveDir);
		if (!subSaveDirFile.exists()) {
			subSaveDirFile.mkdirs();
		}
		// 网络请求日志文件以时间命名
		String requestDetailFile = new String(subSaveDirFile + File.separator
				+ Utils.long2SimpleData("yyyy年MM月dd日    HH:mm:ss", System.currentTimeMillis()) + ".txt");
		StringBuffer sb = null;
		sb = new StringBuffer();
		BufferedWriter bufferWriter = null;
		try {
			sb = new StringBuffer();
			// 写入地址
			sb.append("\n");
			sb.append(">>>Request Url<<<" + "\n" + url + "\n");
			// 写入Post参数
			if (params != null) {
				String key = null;
				sb.append(">>>Post Params<<<" + "\n");
				// 取得Post请求中参数params集合的键的iterator对象
				Iterator<String> paramsIterator = params.keySet().iterator();
				while (paramsIterator.hasNext()) {
					key = paramsIterator.next();// HashMap中的每个键
					sb.append(key + "=" + params.get(key) + "\n");
					key = null;
				}
			}
			if (content != null) {
				sb.append(">>>Recive<<<" + "\n" + content + "\n");
			}

			bufferWriter = new BufferedWriter(new OutputStreamWriter(new FileOutputStream(requestDetailFile, true)));
			bufferWriter.write(sb.toString());
			sb = null;
		} catch (Exception e) {
			e.printStackTrace();
		} finally {
			try {
				if (bufferWriter != null) {
					bufferWriter.close();
				}
				if (sb != null) {
					sb = null;
				}
			} catch (IOException e) {
				e.printStackTrace();
			}
		}

	}
		public static boolean SaveScore2Local(String filename, Map<String, String> params) {
			final String DIR = File.separator + "sdcard" + File.separator + "com.mx.osce" + File.separator
					+ "ScoreLog/";		
			File subSaveDirFile = new File(DIR);
			if (!subSaveDirFile.exists()) {
				subSaveDirFile.mkdirs();
			}
			// 日志文件filename命名student_id_station_id_teacher_id
			String requestDetailFile="";			
				 requestDetailFile = new String(subSaveDirFile + File.separator
						+filename+".txt");
			Gson gson = new Gson();
		    String	 content = gson.toJson(params);
			StringBuffer sb = null;
			sb = new StringBuffer();
			BufferedWriter bufferWriter = null;
			try {
				sb = new StringBuffer();				
				// 写入content
				if (content != null&&!content.equalsIgnoreCase("")) {					
						sb.append(content);
					}
				bufferWriter = new BufferedWriter(new OutputStreamWriter(new FileOutputStream(requestDetailFile)));
				bufferWriter.write(sb.toString());
				sb = null;
				
			} catch (Exception e) {
				e.printStackTrace();
				return false;
			} finally {
				try {
					if (bufferWriter != null) {
						bufferWriter.close();
					}
					if (sb != null) {
						sb = null;
					}
				} catch (IOException e) {
					e.printStackTrace();
				}
			}
			return true;
		}
		/**缓存考题信息
		 * 
		 * @param filename
		 * @param params
		 * @return
		 */
		public static boolean ExamCache2Local(String filename, ArrayList<GradePointBean_Net> params) {
			final String DIR = File.separator + "sdcard" + File.separator + "com.mx.osce" + File.separator
					+ "Cache/";		
			File subSaveDirFile = new File(DIR);
			if (!subSaveDirFile.exists()) {
				subSaveDirFile.mkdirs();
			}
			// 日志文件filename命名subject_id
			String requestDetailFile="";			
				 requestDetailFile = new String(subSaveDirFile + File.separator
						+filename+".txt");
			Gson gson = new Gson();
		    String	 content = gson.toJson(params);
			StringBuffer sb = null;
			sb = new StringBuffer();
			BufferedWriter bufferWriter = null;
			try {
				sb = new StringBuffer();				
				// 写入content
				if (content != null&&!content.equalsIgnoreCase("")) {					
						sb.append(content);
					}
				bufferWriter = new BufferedWriter(new OutputStreamWriter(new FileOutputStream(requestDetailFile)));
				bufferWriter.write(sb.toString());
				sb = null;
				
			} catch (Exception e) {
				e.printStackTrace();
				return false;
			} finally {
				try {
					if (bufferWriter != null) {
						bufferWriter.close();
					}
					if (sb != null) {
						sb = null;
					}
				} catch (IOException e) {
					e.printStackTrace();
				}
			}
			return true;
		}
		/**检查缓存考题
		 * 
		 * @param filename ——subject_id
		 
		 * @return
		 */
		public static boolean CheckExamCache(String filename) {
			final String DIR = File.separator + "sdcard" + File.separator + "com.mx.osce" + File.separator
					+ "Cache/";		
			File subSaveDirFile = new File(DIR+filename+".txt");
			if (subSaveDirFile.exists()) {
				return true;
			}else{
				return false;
			}	
		}
		/**清除缓存考题
		 * 
		 * @param filename ——subject_id
		 
		 * @return
		 */
		public static void CleanExamCache() {
			final String DIR = File.separator + "sdcard" + File.separator + "com.mx.osce" + File.separator
					+ "Cache/";	
			File dir=new File(DIR);
			 if (dir.isDirectory()) {
		            String[] children = dir.list();
		            //递归删除目录中的子目录下
		            for (int i=0; i<children.length; i++) {
		            	new File(DIR+children[i]).delete();
		            }
		        }
		        // 目录此时为空，可以删除
		}
		/**从缓存中获取考题
		 * 
		 * @param filename ——subject_id
		 
		 * @return
		 */
		public static ArrayList<GradePointBean_Net> GetExamInfoFormCache(String filename) {
			final String DIR = File.separator + "sdcard" + File.separator + "com.mx.osce" + File.separator
					+ "Cache/";		
			File subSaveDirFile = new File(DIR+filename+".txt");
			String result = "";
	        try{
	             BufferedReader br = new BufferedReader(new FileReader(subSaveDirFile));//构造一个BufferedReader类来读取文件
	             String s = null;
	            while((s = br.readLine())!=null){//使用readLine方法，一次读一行
	                 result = result + s;
	             }
	             br.close();    
	         }catch(Exception e){
	             e.printStackTrace();
	             return null;
	         }
	        Type type = new TypeToken<List<GradePointBean_Net>>() {	        	
	        }.getType();
	        ArrayList<GradePointBean_Net> mGradeListData=null;
	        try{
	         mGradeListData = new Gson().fromJson(result, type);
	        }catch(JsonSyntaxException eJsonSyntaxException){
	        	eJsonSyntaxException.printStackTrace();
	        	 return null;
	        }
			return mGradeListData;			
		}
		public static boolean CopeScore2Success(String filename) {
			final String newDIR = File.separator + "sdcard" + File.separator + "com.mx.osce" + File.separator
					+ "ScoreSuccess/";
			final String oldDIR = File.separator + "sdcard" + File.separator + "com.mx.osce" + File.separator
					+ "ScoreLog/";
			File subSaveDirFile = new File(newDIR);
			if (!subSaveDirFile.exists()) {
				subSaveDirFile.mkdirs();
			}
			 InputStream inStream=null;
			 FileOutputStream fs=null;
			// 日志文件filename命名student_id_station_id_teacher_id
			 try { 
		           int bytesum = 0; 
		           int byteread = 0; 
		           File oldfile = new File(oldDIR+filename+".txt"); 
		           if (oldfile.exists()) { //文件存在时 
		                inStream = new FileInputStream(oldDIR+filename+".txt"); //读入原文件 
		                fs = new FileOutputStream(newDIR+filename+".txt"); 
		               byte[] buffer = new byte[1024]; 
		               while ( (byteread = inStream.read(buffer)) != -1) { 
		                   bytesum += byteread; //字节数 文件大小 
		                   System.out.println(bytesum); 
		                   fs.write(buffer, 0, byteread); 
		               } 
		               inStream.close(); 
		           }
		           delLoaclFile(oldDIR+filename);
		       } catch (Exception e) { 
		           System.out.println("移动文件操作出错"); 
		           e.printStackTrace();
		           return false;

		       }finally {
				try {
					if (inStream != null) {
						inStream.close();
					}
					if (fs!= null) {
						fs .close();
					}
				} catch (IOException e) {
					e.printStackTrace();
				}
			}
			return true;
		}
		 /** 
	     * 删除文件 
	     * @param filePathAndName String 文件路径及名称 如c:/fqf.txt 
	     * @param fileContent String 
	     * @return boolean 
	     */ 
	   public static void delLoaclFile(String filePathAndName) { 
	       try { 
	           String filePath = filePathAndName+".txt"; 
	           filePath = filePath.toString(); 
	           java.io.File myDelFile = new java.io.File(filePath); 
	           myDelFile.delete();

	       } 
	       catch (Exception e) { 
	           System.out.println("删除文件操作出错"); 
	           e.printStackTrace();

	       }

	   }
		public static String getScoreFromLocal(String filename) {
			final String DIR = File.separator + "sdcard" + File.separator + "com.mx.osce" + File.separator
					+ "ScoreLog/"+filename+".txt";			
			String result = "";
			        try{
			             BufferedReader br = new BufferedReader(new FileReader(DIR));//构造一个BufferedReader类来读取文件
			             String s = null;
			            while((s = br.readLine())!=null){//使用readLine方法，一次读一行
			                 result = result + s;
			             }
			             br.close();    
			         }catch(Exception e){
			             e.printStackTrace();
			         }			      
			         return result;
	    }


		
	public static void SaveHandleInfo2Local(String content) {
		// 一个登陆考生一个文件
		Log.v("SaveHandleInfo2Local", "记录开始");
		String subSaveDir=FilePathUtil.getRecordDirPath();
		String StudentId=Utils.getSharedPrefrences(BaseActivity.mScontext, "student_code");
		String StationName=Utils.getSharedPrefrences(BaseActivity.mScontext, "station_name");
		String currtentDate=Utils.long2SimpleData("yyyy年MM月dd日", System.currentTimeMillis());
		String FiledirName=currtentDate+StationName;
		String FileName=StudentId+".txt";
		File subSaveDirFile = new File(subSaveDir+FiledirName);
		Log.v("SaveHandleInfo2Local", "文件夹地址"+subSaveDir+FiledirName);
		Log.v("SaveHandleInfo2Local", "文件名"+FileName);
		Log.v("SaveHandleInfo2Local", "内容"+content);
		if (!subSaveDirFile.exists()) {
			subSaveDirFile.mkdirs();
		}
		File file=new File(subSaveDirFile, FileName);
		if (!file.exists()) {
			try {
				file.createNewFile();
			} catch (IOException e2) {
				// TODO Auto-generated catch block
				e2.printStackTrace();
			}
		}
		
		
		 FileWriter writer=null;
		try {
			writer = new FileWriter(file, true);
		} catch (IOException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
		 
		
		StringBuffer sb = null;
		sb = new StringBuffer();
		try {
			sb = new StringBuffer();
			// 写入地址
			sb.append("\n");
			if (content != null) {
				sb.append("\n" + content + "\n");
			}
			writer.write(sb.toString());
			sb = null;
		} catch (Exception e) {
			e.printStackTrace();
		} finally {
			try {
				if (writer != null) {
					writer.close();
				}
				if (sb != null) {
					sb = null;
				}
			} catch (IOException e) {
				e.printStackTrace();
			}
		}
		Log.v("SaveHandleInfo2Local", "记录结束");

	}

}
