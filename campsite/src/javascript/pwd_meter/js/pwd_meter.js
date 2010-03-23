/*
**    Original File: pwd_meter.js
**    Created by: Jeff Todnem (http://www.todnem.com/)
**    Created on: 2007-08-14
**    Last modified: 2007-08-30
**
**    License Information:
**    -------------------------------------------------------------------------
**    Copyright (C) 2007 Jeff Todnem
**
**    This program is free software; you can redistribute it and/or modify it
**    under the terms of the GNU General Public License as published by the
**    Free Software Foundation; either version 2 of the License, or (at your
**    option) any later version.
**    
**    This program is distributed in the hope that it will be useful, but
**    WITHOUT ANY WARRANTY; without even the implied warranty of
**    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
**    General Public License for more details.
**    
**    You should have received a copy of the GNU General Public License along
**    with this program; if not, write to the Free Software Foundation, Inc.,
**    59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
**    
**    
*/

function addLoadEvent(func) {
	var oldonload = window.onload;
	if (typeof window.onload != "function") {
		window.onload = func;
	}
	else {
		window.onload = function() {
			if (oldonload) {
				oldonload();
			}
			func();
		};
	}
}

String.prototype.strReverse = function() {
	var newstring = "";
	for (var s=0; s < this.length; s++) {
		newstring = this.charAt(s) + newstring;
	}
	return newstring;
	//strOrig = ' texttotrim ';
	//strReversed = strOrig.revstring();
};

function chkPass(pwd) {
	var oScorebar = document.getElementById("scorebar");
	var oScore = document.getElementById("score");
	var oComplexity = document.getElementById("complexity");
	var nScore = 0;
	var nLength = 0;
	var nAlphaUC = 0;
	var nAlphaLC = 0;
	var nNumber = 0;
	var nSymbol = 0;
	var nMidChar = 0;
	var nRequirements = 0;
	var nAlphasOnly = 0;
	var nNumbersOnly = 0;
	var nRepChar = 0;
	var nConsecAlphaUC = 0;
	var nConsecAlphaLC = 0;
	var nConsecNumber = 0;
	var nConsecSymbol = 0;
	var nConsecCharType = 0;
	var nSeqAlpha = 0;
	var nSeqNumber = 0;
	var nSeqChar = 0;
	var nReqChar = 0;
	var nReqCharType = 3;
	var nMultLength = 4;
	var nMultAlphaUC = 3;
	var nMultAlphaLC = 3;
	var nMultNumber = 4;
	var nMultSymbol = 6;
	var nMultMidChar = 2;
	var nMultRequirements = 2;
	var nMultRepChar = 1;
	var nMultConsecAlphaUC = 2;
	var nMultConsecAlphaLC = 2;
	var nMultConsecNumber = 2;
	var nMultConsecSymbol = 1;
	var nMultConsecCharType = 0;
	var nMultSeqAlpha = 3;
	var nMultSeqNumber = 3;
	var nTmpAlphaUC = "";
	var nTmpAlphaLC = "";
	var nTmpNumber = "";
	var nTmpSymbol = "";
	var sAlphaUC = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	var sAlphaLC = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	var sNumber = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	var sSymbol = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	var sMidChar = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	var sRequirements = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	var sAlphasOnly = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	var sNumbersOnly = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	var sRepChar = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	var sConsecAlphaUC = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	var sConsecAlphaLC = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	var sConsecNumber = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	var sSeqAlpha = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	var sSeqNumber = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	var sAlphas = "abcdefghijklmnopqrstuvwxyz";
	var sNumerics = "01234567890";
	var sComplexity = "Too Short";
	var sStandards = "Below";
	var nMinPwdLen = 8;
	if (document.all) { var nd = 0; } else { var nd = 1; }
	if (pwd) {
		nScore = parseInt(pwd.length * nMultLength);
		nLength = pwd.length;
		var arrPwd = pwd.replace (/\s+/g,"").split(/\s*/);
		var arrPwdLen = arrPwd.length;
		
		/* Loop through password to check for Symbol, Numeric, Lowercase and Uppercase pattern matches */
		for (var a=0; a < arrPwdLen; a++) {
			if (arrPwd[a].match(new RegExp(/[A-Z]/g))) {
				if (nTmpAlphaUC !== "") { if ((nTmpAlphaUC + 1) == a) { nConsecAlphaUC++; nConsecCharType++; } }
				nTmpAlphaUC = a;
				nAlphaUC++;
			}
			else if (arrPwd[a].match(new RegExp(/[a-z]/g))) { 
				if (nTmpAlphaLC !== "") { if ((nTmpAlphaLC + 1) == a) { nConsecAlphaLC++; nConsecCharType++; } }
				nTmpAlphaLC = a;
				nAlphaLC++;
			}
			else if (arrPwd[a].match(new RegExp(/[0-9]/g))) { 
				if (a > 0 && a < (arrPwdLen - 1)) { nMidChar++; }
				if (nTmpNumber !== "") { if ((nTmpNumber + 1) == a) { nConsecNumber++; nConsecCharType++; } }
				nTmpNumber = a;
				nNumber++;
			}
			else if (arrPwd[a].match(new RegExp(/[^a-zA-Z0-9_]/g))) { 
				if (a > 0 && a < (arrPwdLen - 1)) { nMidChar++; }
				if (nTmpSymbol !== "") { if ((nTmpSymbol + 1) == a) { nConsecSymbol++; nConsecCharType++; } }
				nTmpSymbol = a;
				nSymbol++;
			}
			/* Internal loop through password to check for repeated characters */
			for (var b=0; b < arrPwdLen; b++) {
				if (arrPwd[a].toLowerCase() == arrPwd[b].toLowerCase() && a != b) { nRepChar++; }
			}
		}
		
		/* Check for sequential alpha string patterns (forward and reverse) */
		for (var s=0; s < 23; s++) {
			var sFwd = sAlphas.substring(s,parseInt(s+3));
			var sRev = sFwd.strReverse();
			if (pwd.toLowerCase().indexOf(sFwd) != -1 || pwd.toLowerCase().indexOf(sRev) != -1) { nSeqAlpha++; nSeqChar++;}
		}
		
		/* Check for sequential numeric string patterns (forward and reverse) */
		for (var s=0; s < 8; s++) {
			var sFwd = sNumerics.substring(s,parseInt(s+3));
			var sRev = sFwd.strReverse();
			if (pwd.toLowerCase().indexOf(sFwd) != -1 || pwd.toLowerCase().indexOf(sRev) != -1) { nSeqNumber++; nSeqChar++;}
		}
		
	/* Modify overall score value based on usage vs requirements */

		/* General point assignment */
		document.getElementById("nLengthBonus").innerHTML = "+ " + nScore; 
		if (nAlphaUC > 0 && nAlphaUC < nLength) {	
			nScore = parseInt(nScore + ((nLength - nAlphaUC) * 2));
			sAlphaUC = "+ " + parseInt((nLength - nAlphaUC) * 2); 
		}
		if (nAlphaLC > 0 && nAlphaLC < nLength) {	
			nScore = parseInt(nScore + ((nLength - nAlphaLC) * 2)); 
			sAlphaLC = "+ " + parseInt((nLength - nAlphaLC) * 2);
		}
		if (nNumber > 0 && nNumber < nLength) {	
			nScore = parseInt(nScore + (nNumber * nMultNumber));
			sNumber = "+ " + parseInt(nNumber * nMultNumber);
		}
		if (nSymbol > 0) {	
			nScore = parseInt(nScore + (nSymbol * nMultSymbol));
			sSymbol = "+ " + parseInt(nSymbol * nMultSymbol);
		}
		if (nMidChar > 0) {	
			nScore = parseInt(nScore + (nMidChar * nMultMidChar));
			sMidChar = "+ " + parseInt(nMidChar * nMultMidChar);
		}
		document.getElementById("nAlphaUCBonus").innerHTML = sAlphaUC; 
		document.getElementById("nAlphaLCBonus").innerHTML = sAlphaLC;
		document.getElementById("nNumberBonus").innerHTML = sNumber;
		document.getElementById("nSymbolBonus").innerHTML = sSymbol;
		document.getElementById("nMidCharBonus").innerHTML = sMidChar;
		
		/* Point deductions for poor practices */
		if ((nAlphaLC > 0 || nAlphaUC > 0) && nSymbol === 0 && nNumber === 0) {  // Only Letters
			nScore = parseInt(nScore - nLength);
			nAlphasOnly = nLength;
			sAlphasOnly = "- " + nLength;
		}
		if (nAlphaLC === 0 && nAlphaUC === 0 && nSymbol === 0 && nNumber > 0) {  // Only Numbers
			nScore = parseInt(nScore - nLength); 
			nNumbersOnly = nLength;
			sNumbersOnly = "- " + nLength;
		}
		if (nRepChar > 0) {  // Same character exists more than once
			nScore = parseInt(nScore - (nRepChar * nRepChar));
			sRepChar = "- " + nRepChar;
		}
		if (nConsecAlphaUC > 0) {  // Consecutive Uppercase Letters exist
			nScore = parseInt(nScore - (nConsecAlphaUC * nMultConsecAlphaUC)); 
			sConsecAlphaUC = "- " + parseInt(nConsecAlphaUC * nMultConsecAlphaUC);
		}
		if (nConsecAlphaLC > 0) {  // Consecutive Lowercase Letters exist
			nScore = parseInt(nScore - (nConsecAlphaLC * nMultConsecAlphaLC)); 
			sConsecAlphaLC = "- " + parseInt(nConsecAlphaLC * nMultConsecAlphaLC);
		}
		if (nConsecNumber > 0) {  // Consecutive Numbers exist
			nScore = parseInt(nScore - (nConsecNumber * nMultConsecNumber));  
			sConsecNumber = "- " + parseInt(nConsecNumber * nMultConsecNumber);
		}
		if (nSeqAlpha > 0) {  // Sequential alpha strings exist (3 characters or more)
			nScore = parseInt(nScore - (nSeqAlpha * nMultSeqAlpha)); 
			sSeqAlpha = "- " + parseInt(nSeqAlpha * nMultSeqAlpha);
		}
		if (nSeqNumber > 0) {  // Sequential numeric strings exist (3 characters or more)
			nScore = parseInt(nScore - (nSeqNumber * nMultSeqNumber)); 
			sSeqNumber = "- " + parseInt(nSeqNumber * nMultSeqNumber);
		}
		document.getElementById("nAlphasOnlyBonus").innerHTML = sAlphasOnly; 
		document.getElementById("nNumbersOnlyBonus").innerHTML = sNumbersOnly; 
		document.getElementById("nRepCharBonus").innerHTML = sRepChar; 
		document.getElementById("nConsecAlphaUCBonus").innerHTML = sConsecAlphaUC; 
		document.getElementById("nConsecAlphaLCBonus").innerHTML = sConsecAlphaLC; 
		document.getElementById("nConsecNumberBonus").innerHTML = sConsecNumber;
		document.getElementById("nSeqAlphaBonus").innerHTML = sSeqAlpha; 
		document.getElementById("nSeqNumberBonus").innerHTML = sSeqNumber; 

		/* Determine if mandatory requirements have been met and set image indicators accordingly */
		var arrChars = [nLength,nAlphaUC,nAlphaLC,nNumber,nSymbol];
		var arrCharsIds = ["nLength","nAlphaUC","nAlphaLC","nNumber","nSymbol"];
		var arrCharsLen = arrChars.length;
		for (var c=0; c < arrCharsLen; c++) {
			var oImg = document.getElementById('div_' + arrCharsIds[c]);
			var oBonus = document.getElementById(arrCharsIds[c] + 'Bonus');
			document.getElementById(arrCharsIds[c]).innerHTML = arrChars[c];
			if (arrCharsIds[c] == "nLength") { var minVal = parseInt(nMinPwdLen - 1); } else { var minVal = 0; }
			if (arrChars[c] == parseInt(minVal + 1)) { nReqChar++; oImg.className = "pass"; oBonus.parentNode.className = "pass"; }
			else if (arrChars[c] > parseInt(minVal + 1)) { nReqChar++; oImg.className = "exceed"; oBonus.parentNode.className = "exceed"; }
			else { oImg.className = "fail"; oBonus.parentNode.className = "fail"; }
		}
		nRequirements = nReqChar;
		if (pwd.length >= nMinPwdLen) { var nMinReqChars = 3; } else { var nMinReqChars = 4; }
		if (nRequirements > nMinReqChars) {  // One or more required characters exist
			nScore = parseInt(nScore + (nRequirements * 2)); 
			sRequirements = "+ " + parseInt(nRequirements * 2);
		}
		document.getElementById("nRequirementsBonus").innerHTML = sRequirements;

		/* Determine if additional bonuses need to be applied and set image indicators accordingly */
		var arrChars = [nMidChar,nRequirements];
		var arrCharsIds = ["nMidChar","nRequirements"];
		var arrCharsLen = arrChars.length;
		for (var c=0; c < arrCharsLen; c++) {
			var oImg = document.getElementById('div_' + arrCharsIds[c]);
			var oBonus = document.getElementById(arrCharsIds[c] + 'Bonus');
			document.getElementById(arrCharsIds[c]).innerHTML = arrChars[c];
			if (arrCharsIds[c] == "nRequirements") { var minVal = nMinReqChars; } else { var minVal = 0; }
			if (arrChars[c] == parseInt(minVal + 1)) { oImg.className = "pass"; oBonus.parentNode.className = "pass"; }
			else if (arrChars[c] > parseInt(minVal + 1)) { oImg.className = "exceed"; oBonus.parentNode.className = "exceed"; }
			else { oImg.className = "fail"; oBonus.parentNode.className = "fail"; }
		}

		/* Determine if suggested requirements have been met and set image indicators accordingly */
		var arrChars = [nAlphasOnly,nNumbersOnly,nRepChar,nConsecAlphaUC,nConsecAlphaLC,nConsecNumber,nSeqAlpha,nSeqNumber];
		var arrCharsIds = ["nAlphasOnly","nNumbersOnly","nRepChar","nConsecAlphaUC","nConsecAlphaLC","nConsecNumber","nSeqAlpha","nSeqNumber"];
		var arrCharsLen = arrChars.length;
		for (var c=0; c < arrCharsLen; c++) {
			var oImg = document.getElementById('div_' + arrCharsIds[c]);
			var oBonus = document.getElementById(arrCharsIds[c] + 'Bonus');
			document.getElementById(arrCharsIds[c]).innerHTML = arrChars[c];
			if (arrChars[c] > 0) { oImg.className = "warn"; oBonus.parentNode.className = "warn"; }
			else { oImg.className = "pass"; oBonus.parentNode.className = "pass"; }
		}
		
		/* Determine complexity based on overall score */
		if (nScore > 100) { nScore = 100; } else if (nScore < 0) { nScore = 0; }
		if (nScore >= 0 && nScore < 20) { sComplexity = "Very Weak"; }
		else if (nScore >= 20 && nScore < 40) { sComplexity = "Weak"; }
		else if (nScore >= 40 && nScore < 60) { sComplexity = "Good"; }
		else if (nScore >= 60 && nScore < 80) { sComplexity = "Strong"; }
		else if (nScore >= 80 && nScore <= 100) { sComplexity = "Very Strong"; }
		
		/* Display updated score criteria to client */
		oScorebar.style.backgroundPosition = "-" + parseInt(nScore * 4) + "px";
		oScore.innerHTML = nScore + "%";
		oComplexity.innerHTML = sComplexity;
	}
	else {
		/* Display default score criteria to client */
		initPwdChk();
		oScore.innerHTML = nScore + "%";
		oComplexity.innerHTML = sComplexity;
	}
}

function togPwdMask() {
	var oPwd = document.getElementById("password");
	var oTxt = document.getElementById("passwordTxt");
	var oMask = document.getElementById("mask");
	if (oMask.checked) { 
		oPwd.value = oTxt.value;
		oPwd.className = ""; 
		oTxt.className = "hide"; 
	} 
	else { 
		oTxt.value = oPwd.value;
		oPwd.className = "hide"; 
		oTxt.className = "";
	}
}

function initPwdChk(restart) {
	/* Reset all form values to their default */
	document.getElementById("nLength").innerHTML = "0";
	document.getElementById("nAlphaUC").innerHTML = "0";
	document.getElementById("nAlphaLC").innerHTML = "0";
	document.getElementById("nNumber").innerHTML = "0";
	document.getElementById("nSymbol").innerHTML = "0";
	document.getElementById("nMidChar").innerHTML = "0";
	document.getElementById("nRequirements").innerHTML = "0";
	document.getElementById("nAlphasOnly").innerHTML = "0";
	document.getElementById("nNumbersOnly").innerHTML = "0";
	document.getElementById("nRepChar").innerHTML = "0";
	document.getElementById("nConsecAlphaUC").innerHTML = "0";
	document.getElementById("nConsecAlphaLC").innerHTML = "0";
	document.getElementById("nConsecNumber").innerHTML = "0";
	document.getElementById("nSeqAlpha").innerHTML = "0";
	document.getElementById("nSeqNumber").innerHTML = "0";
	document.getElementById("nLengthBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nAlphaUCBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nAlphaLCBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nNumberBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nSymbolBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nMidCharBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nRequirementsBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nAlphasOnlyBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nNumbersOnlyBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nRepCharBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nConsecAlphaUCBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nConsecAlphaLCBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nConsecNumberBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nSeqAlphaBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nSeqNumberBonus").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;0";
	document.getElementById("nLengthBonus").parentNode.className = "fail";
	document.getElementById("nAlphaUCBonus").parentNode.className = "fail";
	document.getElementById("nAlphaLCBonus").parentNode.className = "fail";
	document.getElementById("nNumberBonus").parentNode.className = "fail";
	document.getElementById("nSymbolBonus").parentNode.className = "fail";
	document.getElementById("nMidCharBonus").parentNode.className = "fail";
	document.getElementById("nRequirementsBonus").parentNode.className = "fail";
	document.getElementById("nAlphasOnlyBonus").parentNode.className = "pass";
	document.getElementById("nNumbersOnlyBonus").parentNode.className = "pass";
	document.getElementById("nRepCharBonus").parentNode.className = "pass";
	document.getElementById("nConsecAlphaUCBonus").parentNode.className = "pass";
	document.getElementById("nConsecAlphaLCBonus").parentNode.className = "pass";
	document.getElementById("nConsecNumberBonus").parentNode.className = "pass";
	document.getElementById("nSeqAlphaBonus").parentNode.className = "pass";
	document.getElementById("nSeqNumberBonus").parentNode.className = "pass";
	document.getElementById("div_nLength").className = "fail";
	document.getElementById("div_nAlphaUC").className = "fail";
	document.getElementById("div_nAlphaLC").className = "fail";
	document.getElementById("div_nNumber").className = "fail";
	document.getElementById("div_nSymbol").className = "fail";
	document.getElementById("div_nMidChar").className = "fail";
	document.getElementById("div_nRequirements").className = "fail";
	document.getElementById("div_nAlphasOnly").className = "pass";
	document.getElementById("div_nNumbersOnly").className = "pass";
	document.getElementById("div_nRepChar").className = "pass";
	document.getElementById("div_nConsecAlphaUC").className = "pass";
	document.getElementById("div_nConsecAlphaLC").className = "pass";
	document.getElementById("div_nConsecNumber").className = "pass";
	document.getElementById("div_nSeqAlpha").className = "pass";
	document.getElementById("div_nSeqNumber").className = "pass";
	document.getElementById("password").value = "";
	document.getElementById("passwordTxt").value = "";
	document.getElementById("scorebar").style.backgroundPosition = "0";
	if (restart) {
		document.getElementById("password").className = "";
		document.getElementById("passwordTxt").className = "hide";
		document.getElementById("mask").checked = true;
	}
}

addLoadEvent(function() { initPwdChk(1); });

