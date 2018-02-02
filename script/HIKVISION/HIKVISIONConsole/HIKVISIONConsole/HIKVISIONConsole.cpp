// HIKVISIONConsole.cpp : Defines the entry point for the console application.
//

#include "stdafx.h"
#include <iostream>
#include <afx.h>
#include <process.h>
#include "Windows.h"
#include "GeneralDef.h"
#include "FormatConversionInterface.h"

#define MAX_STRING_LEN 100

#define TRANSFORM_PS_MODE 1
#define TRANSFORM_TS_MODE 2
#define TRANSFORM_RTP_MODE 3

using namespace std;

/************************************************************************/
/*
Author: Misrobot.
Date: 2016-4-21
Version: V1.0.1
1.Add the function which can scan the basic parameter.
Version: V1.0.2
1.Fix the Bug of parameter pass.
Version: V1.0.3
1.Add the .MP4 format convert.
*/
/************************************************************************/

/******************************Function Declare*********************************/
void UnitTest();

int LogIn(char * pIP, int iServicePort, \
	char *  pAdmin, char * pPwd, \
	NET_DVR_DEVICEINFO_V30 & deviceInfo);

bool SearchAndDownload(NET_DVR_TIME startTime, NET_DVR_TIME stopTime, \
	int userID, NET_DVR_DEVICEINFO_V30 deviceInfo, char *fName);

bool Save(NET_DVR_TIME startDateTimeDst, NET_DVR_TIME stopDateTimeDst, \
	int userID, NET_DVR_DEVICEINFO_V30 deviceInfo);

void GetStartStopTime(CTime startDateTimeSrc, CTime stopDateTimeSrc, \
	NET_DVR_TIME &startDateTimeDst, NET_DVR_TIME &stopDateTimeDst);

void GetStartStopTime(NET_DVR_TIME startDateTimeSrc, NET_DVR_TIME stopDateTimeSrc, \
	CTime &startDateTimeDst, CTime &stopDateTimeDst);

bool FormatConvert(char* srcPath, char* dstPath);

string GetCurrentPath();
/*******************************************************************************/

int main(int argc, char** argv[])
{
	//Test Function.
	UnitTest();
	// 	Device Information.
	char ip[MAX_STRING_LEN];
	char iServerPort[MAX_STRING_LEN];
	char user[MAX_STRING_LEN];
	char pwd[MAX_STRING_LEN];

	int startYear = 0;
	int startMonth = 0;
	int startDay = 0;
	int startHour = 0;
	int startMinute = 0;
	int startSecond = 0;

	int endYear = 0;
	int endMonth = 0;
	int endDay = 0;
	int endHour = 0;
	int endMinute = 0;
	int endSecond = 0;

	memset(ip, 0x00, sizeof(ip));
	memset(user, 0x00, sizeof(user));
	memset(pwd, 0x00, sizeof(pwd));
	memset(iServerPort, 0x00, sizeof(iServerPort));

	int iCounter = 1;//The start index.
	//IP Address.

	if ((argv[iCounter]) == NULL)
	{
		printf_s("Parse IP Error!");
		return 0;
	}
	memcpy(ip, &(*argv[iCounter]), MAX_STRING_LEN);
	printf_s("The device IP:\n %s\n", ip);
	iCounter++;

	//Server Port.
	if ((argv[iCounter]) == NULL)
	{
		printf_s("Parse Server Port Error!");
		return 0;
	}
	memcpy(iServerPort, &(*argv[iCounter]), sizeof(int));
	printf_s("The server port:\n %s\n", iServerPort);
	iCounter++;
	//User.
	if ((argv[iCounter]) == NULL)
	{
		printf_s("Parse User Error!");
		return 0;
	}
	memcpy(user, &(*argv[iCounter]), MAX_STRING_LEN);
	printf_s("The user:\n %s\n", user);
	iCounter++;

	//PassWord.
	if ((argv[iCounter]) == NULL)
	{
		printf_s("Parse Password Error!");
		return 0;
	}
	memcpy(pwd, &(*argv[iCounter]), MAX_STRING_LEN);
	printf_s("The password:\n %s\n", pwd);
	iCounter++;

	//StartTime.
	if ((argv[iCounter]) == NULL)
	{
		printf_s("Parse StartTime Error!");
		return 0;
	}
	char temp[100];
	memset(temp, 0x00, sizeof(temp));
	memcpy(temp, &(*argv[iCounter]), sizeof(temp));
	startYear = atoi(temp);
	iCounter++;
	memset(temp, 0x00, sizeof(temp));
	memcpy(temp, &(*argv[iCounter]), sizeof(temp));
	startMonth = atoi(temp);
	iCounter++;
	memset(temp, 0x00, sizeof(temp));
	memcpy(temp, &(*argv[iCounter]), sizeof(temp));
	startDay = atoi(temp);
	iCounter++;
	memset(temp, 0x00, sizeof(temp));
	memcpy(temp, &(*argv[iCounter]), sizeof(temp));
	startHour = atoi(temp);
	iCounter++;
	memset(temp, 0x00, sizeof(temp));
	memcpy(temp, &(*argv[iCounter]), sizeof(temp));
	startMinute = atoi(temp);
	iCounter++;
	memset(temp, 0x00, sizeof(temp));
	memcpy(temp, &(*argv[iCounter]), sizeof(temp));
	startSecond = atoi(temp);
	iCounter++;
	printf_s("The start time: \n %d-%d-%d %d:%d:%d\n", startYear, startMonth, startDay, startHour, startMinute, startSecond);
	CTime startDateTime = CTime(startYear, startMonth, startDay, startHour, startMinute, startSecond);
	//EndTime.
	if ((argv[iCounter]) == NULL)
	{
		printf_s("Parse EndTime Error!");
		return 0;
	}
	memset(temp, 0x00, sizeof(temp));
	memcpy(temp, &(*argv[iCounter]), sizeof(temp));
	endYear = atoi(temp);
	iCounter++;
	memset(temp, 0x00, sizeof(temp));
	memcpy(temp, &(*argv[iCounter]), sizeof(temp));
	endMonth = atoi(temp);
	iCounter++;
	memset(temp, 0x00, sizeof(temp));
	memcpy(temp, &(*argv[iCounter]), sizeof(temp));
	endDay = atoi(temp);
	iCounter++;
	memset(temp, 0x00, sizeof(temp));
	memcpy(temp, &(*argv[iCounter]), sizeof(temp));
	endHour = atoi(temp);
	iCounter++;
	memset(temp, 0x00, sizeof(temp));
	memcpy(temp, &(*argv[iCounter]), sizeof(temp));
	endMinute = atoi(temp);
	iCounter++;
	memset(temp, 0x00, sizeof(temp));
	memcpy(temp, &(*argv[iCounter]), sizeof(temp));
	endSecond = atoi(temp);
	iCounter++;
	printf_s("The end time: \n %d-%d-%d %d:%d:%d\n", endYear, endMonth, endDay, endHour, endMinute, endSecond);
	CTime stopDateTime = CTime(endYear, endMonth, endDay, endHour, endMinute, endSecond);

	//Init the SDK.
	NET_DVR_Init();
	NET_DVR_DEVICEINFO_V30 deviceInfo;
	//Log in the device.
	int userID = LogIn(ip, atoi(iServerPort), user, pwd, deviceInfo);
	//Convert to SDK-custom Time format.
	NET_DVR_TIME startDateTimeDst;
	NET_DVR_TIME stopDateTimeDst;
	GetStartStopTime(startDateTime, stopDateTime, \
		startDateTimeDst, stopDateTimeDst);
	//File Name.
	char fName[100] = { 0 };
	sprintf_s(fName, \
		"%d-%02d-%02d %02d-%02d-%02d_%d-%02d-%02d %02d-%02d-%02d.mp4", \
		startDateTimeDst.dwYear, \
		startDateTimeDst.dwMonth, \
		startDateTimeDst.dwDay, \
		startDateTimeDst.dwHour, \
		startDateTimeDst.dwMinute, \
		startDateTimeDst.dwSecond, \
		stopDateTimeDst.dwYear, \
		stopDateTimeDst.dwMonth, \
		stopDateTimeDst.dwDay, \
		stopDateTimeDst.dwHour, \
		stopDateTimeDst.dwMinute, \
		stopDateTimeDst.dwSecond);
	string srcPath = GetCurrentPath() + "Download_" + fName;
	string dstPath = GetCurrentPath() + fName;

	//Search the file.
	SearchAndDownload(startDateTimeDst, stopDateTimeDst, \
		userID, deviceInfo, (char*)(srcPath.c_str()));

	// 	Save(startDateTimeDst, stopDateTimeDst, \
						   	  	// 		userID, deviceInfo);

	NET_DVR_Logout_V30(userID);
	//Clean the data.
	NET_DVR_Cleanup();
	printf_s("DownLoad completed, Converting...\n");
	//Convert.
	CString strDeleteFilePath;
	if (FormatConvert((char*)(srcPath.c_str()), (char*)(dstPath.c_str()))) {
		printf_s("The Video Convert Successfully!\n");
		strDeleteFilePath = srcPath.c_str();
	}
	else {
		printf_s("The Video Convert Failure!\n");
		strDeleteFilePath = dstPath.c_str();
	}
	::DeleteFile(strDeleteFilePath);

	return 0;
}

//Log in the device.
int LogIn(char * pIP, int iServicePort, char *  pAdmin, char * pPwd, NET_DVR_DEVICEINFO_V30 & deviceInfo)
{
	NET_DVR_DEVICEINFO_V30 DeviceInfoTmp;
	memset(&DeviceInfoTmp, 0, sizeof(NET_DVR_DEVICEINFO_V30));

	int lLoginID = NET_DVR_Login_V30(pIP, \
		(WORD)iServicePort, \
		pAdmin, \
		pPwd, \
		&DeviceInfoTmp);

	if (lLoginID == -1)
	{
		printf("Login to Device failed!\n");
		TRACE("NET_DVR_Login_V30 failed! Error code:%d\n", NET_DVR_GetLastError());
	}
	else
	{
		deviceInfo.byIPChanNum = DeviceInfoTmp.byIPChanNum;
		if (DeviceInfoTmp.byChanNum > 0) {
			deviceInfo.byStartChan = DeviceInfoTmp.byStartChan;
			deviceInfo.byChanNum = DeviceInfoTmp.byChanNum;
		}
		else if (DeviceInfoTmp.byIPChanNum > 0) {
			deviceInfo.byStartChan = DeviceInfoTmp.byStartDChan;
			deviceInfo.byChanNum = DeviceInfoTmp.byIPChanNum + DeviceInfoTmp.byHighDChanNum * 256;
		}
	}

	return lLoginID;
}

//Search the video files. 
bool SearchAndDownload(NET_DVR_TIME startTime, NET_DVR_TIME stopTime, \
	int userID, NET_DVR_DEVICEINFO_V30 deviceInfo, char *fName)
{
	if (deviceInfo.byStartChan == -1)
	{
		printf("Please select a channel!\n");
		return false;
	}

	int channel = deviceInfo.byStartChan;

	int DownloadHandle = NET_DVR_GetFileByTime(userID, channel, &startTime, &stopTime, fName);

	if (DownloadHandle == -1)
	{
		TRACE("Error code:%d\n", NET_DVR_GetLastError());
		return false;
	}

	if (NET_DVR_PlayBackControl(DownloadHandle, NET_DVR_SET_TRANS_TYPE, TRANSFORM_PS_MODE, NULL))
		printf("Get video success!\n");
	else
		printf("Get video failure!\n");

	printf("The program is downloading...\n");

	LONG iTimeCounter = 0;
	LONG iTimeMaxLen = (stopTime.dwYear - startTime.dwYear) * 365 * 24 * 3600 +
		(stopTime.dwMonth - startTime.dwMonth) * 31 * 24 * 3600 +
		(stopTime.dwDay - startTime.dwDay) * 24 * 3600 +
		(stopTime.dwHour - startTime.dwHour) * 60 * 60 +
		(stopTime.dwMinute - startTime.dwMinute) * 60 +
		(stopTime.dwSecond - startTime.dwSecond);

	while (NET_DVR_PlayBackControl(DownloadHandle, NET_DVR_PLAYSTART, 0, NULL)) {//DownLoad Delay...
		Sleep(1000);
		iTimeCounter++;
		if (iTimeCounter >= iTimeMaxLen)//Timeout.
		{
			printf("Delay Time is too long, The System is terminate automatically...\n");
			break;
		}
	}

	printf("Download Complete!\n");

	return true;
}

//Save the files.
bool Save(NET_DVR_TIME startDateTimeDst, NET_DVR_TIME stopDateTimeDst, int userID, NET_DVR_DEVICEINFO_V30 deviceInfo)
{
	char RecName[256] = { 0 };
	CTime CurTime = CTime::GetCurrentTime();

	sprintf_s(RecName, "Save_%04d-%02d-%02d %02d-%02d-%02d.mp4", \
		CurTime.GetYear(), CurTime.GetMonth(), CurTime.GetDay(), CurTime.GetHour(), CurTime.GetMinute(), CurTime.GetSecond());

	int playHandle = NET_DVR_PlayBackByTime(userID, deviceInfo.byStartChan, &startDateTimeDst, &stopDateTimeDst, NULL);

	if (playHandle == -1)
	{
		printf("Failure to play back!\n");
		return false;
	}

	if (NET_DVR_PlayBackSaveData(playHandle, RecName))
	{
		return true;
	}
	else
	{
		TRACE("Error code:%d\n", NET_DVR_GetLastError());
		printf("Failure to save the file!\n");
		NET_DVR_StopPlayBackSave(playHandle);
		return false;
	}
}

//DateTime Format Convert.
void GetStartStopTime(CTime startDateTimeSrc, CTime stopDateTimeSrc, \
	NET_DVR_TIME &startDateTimeDst, NET_DVR_TIME &stopDateTimeDst)
{
	startDateTimeDst.dwYear = startDateTimeSrc.GetYear();
	startDateTimeDst.dwMonth = startDateTimeSrc.GetMonth();
	startDateTimeDst.dwDay = startDateTimeSrc.GetDay();
	startDateTimeDst.dwHour = startDateTimeSrc.GetHour();
	startDateTimeDst.dwMinute = startDateTimeSrc.GetMinute();
	startDateTimeDst.dwSecond = startDateTimeSrc.GetSecond();

	stopDateTimeDst.dwYear = stopDateTimeSrc.GetYear();
	stopDateTimeDst.dwMonth = stopDateTimeSrc.GetMonth();
	stopDateTimeDst.dwDay = stopDateTimeSrc.GetDay();
	stopDateTimeDst.dwHour = stopDateTimeSrc.GetHour();
	stopDateTimeDst.dwMinute = stopDateTimeSrc.GetMinute();
	stopDateTimeDst.dwSecond = stopDateTimeSrc.GetSecond();
}

//DateTime Format Convert.
void GetStartStopTime(NET_DVR_TIME startDateTimeSrc, NET_DVR_TIME stopDateTimeSrc, \
	CTime &startDateTimeDst, CTime &stopDateTimeDst)
{
	startDateTimeDst = CTime(startDateTimeSrc.dwYear, \
		startDateTimeSrc.dwMonth, \
		startDateTimeSrc.dwDay, \
		startDateTimeSrc.dwHour, \
		startDateTimeSrc.dwMinute, \
		startDateTimeSrc.dwSecond);

	stopDateTimeDst = CTime(stopDateTimeSrc.dwYear, \
		stopDateTimeSrc.dwMonth, \
		stopDateTimeSrc.dwDay, \
		stopDateTimeSrc.dwHour, \
		stopDateTimeSrc.dwMinute, \
		stopDateTimeSrc.dwSecond);
}

//Convert Format.
bool FormatConvert(char* srcPath, char* dstPath) {
	//
	FC_MEDIA_INFO MediaInfo;
	memset(&MediaInfo, 0x00, sizeof(MediaInfo));
	MediaInfo.enSystemFormat = FC_FORMAT_MP4;//   FC_FORMAT_SWF
	MediaInfo.nVideoStreamCount = 1;
	MediaInfo.nAudioStreamCount = 1;
	MediaInfo.nPrivtStreamCount = 0;
	//
	FCHANDLE Handle = FC_CreateHandle();

	if (FC_SetTargetMediaInfo(Handle, &MediaInfo) != FC_OK)
	{
		return false;
	}

	if (FC_Start(Handle, srcPath, dstPath) != FC_OK)
	{
		return false;
	}

	float progress = 0;
	float *pProgress = &progress;
	FC_GetProgress(Handle, pProgress);
	while (*pProgress < 1.0) {
		Sleep(1000);
		FC_GetProgress(Handle, pProgress);
	}

	FC_Stop(Handle);

	FC_DestroyHandle(Handle);

	return true;
}

//Get Current Path.
string GetCurrentPath()
{
	// 	CString strPath;
	// 	GetModuleFileName(NULL, strPath.GetBufferSetLength(MAX_PATH + 1), MAX_PATH);
	// 	strPath.ReleaseBuffer();
	// 	int nPos;
	// 	nPos = strPath.ReverseFind('\\');
	// 	strPath = strPath.Left(nPos);
	// 	return strPath;

	// 	TCHAR szDir[MAX_PATH];
	// 	memset(szDir, 0, MAX_PATH);
	// 	return ::GetCurrentDirectory(MAX_PATH, szDir);

	char szFilePath[MAX_PATH + 1] = { 0 };
	GetModuleFileNameA(NULL, szFilePath, MAX_PATH);
	(strrchr(szFilePath, '\\') + 1)[0] = 0;
	string path = szFilePath;

	return path;
}

//Unit Test.
void UnitTest() {
	//...
	//Convert.
	//  	string path = GetCurrentPath() + "Î´×ª¹ýµÄMP4.mp4";//VID_20160603_173710.mp4
	//  	BOOL BB;
	//  	if (FormatConvert((char*)(path.c_str()), (char*)((GetCurrentPath() + "finish.mp4").c_str())))
	//  	{
	//  		printf_s("The Video Convert Successfully!");
	//  	}
	//  	else
	//  	{
	//  		printf_s("The Video Convert Failure!");
	//  	}
}
