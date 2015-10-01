/************************************************
File: pcropsev1
Location: looksur
Title: Regional severity of OSR diseases (Autumn)
       (Mean percentage area affected)
*************************************************/

/*****************************************************
A conversion from Informix 4GL report generated output
to MySQL queries to report the same.
******************************************************/


SET  @yearMin = 2003
GO
SET  @yearMax = 2003
GO
SET  @treat = UPPER("n")
GO

/* Create work table */
DROP TEMPORARY TABLE IF  EXISTS tmp_all
GO
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_all
SELECT
   mleaf1.msample_no, maut_mil, maut_lls, maut_wls, maut_alt,
   maut_pho,
   maut_pm, maut_rs,
   maut_bot, farm_no, syear, pcrop1

FROM
   mleaf1, farm
WHERE mleaf1.msample_no = farm.farm_no
AND syear BETWEEN @yearMin AND @yearMax
   and untreat = @treat
GO


SELECT COUNT(*) FROM tmp_all INTO @total_samples
GO

/******************************************************
Gather the information into the work table
   Use queries to display results that were previously
   presented using 4GL report functions
******************************************************/

/* National totals */
DROP TEMPORARY TABLE IF  EXISTS tmp_results
GO
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_results
SELECT
  pcrop1 AS pcrop1,
  COALESCE(COUNT(*),0) AS "No. of Results",
  FORMAT(COALESCE(AVG(maut_mil),0),3) AS mil,
  FORMAT(COALESCE(AVG(maut_lls),0),3) AS lls,
  FORMAT(COALESCE(AVG(maut_wls),0),3) AS wls,
  FORMAT(COALESCE(AVG(maut_alt),0),3) AS alt,
  FORMAT(COALESCE(AVG(maut_pho),0),3) AS pho,
  FORMAT(COALESCE(AVG(maut_pm),0),3) AS pm,
  FORMAT(COALESCE(AVG(maut_rs),0),3) AS rs,
  FORMAT(COALESCE(AVG(maut_bot),0),3) AS bot
FROM tmp_all
GROUP BY pcrop1
GO

/* National averages */

SELECT * FROM tmp_results
UNION
SELECT
  "National",
COALESCE(@total_samples,0),
FORMAT(COALESCE(SUM(maut_mil)/@total_samples,0),3),
FORMAT(COALESCE(SUM(maut_lls)/@total_samples,0),3),
FORMAT(COALESCE(SUM(maut_wls)/@total_samples,0),3),
FORMAT(COALESCE(SUM(maut_alt)/@total_samples,0),3),
FORMAT(COALESCE(SUM(maut_pho)/@total_samples,0),3),
FORMAT(COALESCE(SUM(maut_pm)/@total_samples,0),3),
FORMAT(COALESCE(SUM(maut_rs)/@total_samples,0),3),
FORMAT(COALESCE(SUM(maut_bot)/@total_samples,0),3)
FROM
tmp_all
