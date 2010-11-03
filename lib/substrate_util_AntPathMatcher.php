<?php
require_once('substrate_util_IPathMatcher.php');

class substrate_util_AntPathMatcher implements substrate_util_IPathMatcher {
    
    /**
     * Determine if specified path matches a pattern
     * Copied from Hole Security ( https://github.com/aek/hole-security/ )
     * Based on pop-php
     * @see PurSelector#match($pattern, $path)
     */
    public function match($pattern, $path, $isCaseSensitive = false){
        $patArr = $pattern;
        $pathArr = $path;
        $patIdxStart = 0;
        $patIdxEnd = strlen($patArr) - 1;
        $pathIdxStart = 0;
        $pathIdxEnd = strlen($pathArr) - 1;
        $ch;

        $containsStar = false;
        for ($i = 0; $i < strlen($patArr); $i++) {
            if ($patArr[$i] == '*') {
                $containsStar = true;
                break;
            }
        }

        if (!$containsStar) {
            // No '*'s, so we make a shortcut
            if ($patIdxEnd != $pathIdxEnd) {
                return false; // Pattern and string do not have the same size
            }
            for ($i = 0; $i <= $patIdxEnd; $i++) {
                $ch = $patArr[$i];
                if ($ch != '?') {
                    if ($isCaseSensitive && $ch != $pathArr[$i]) {
                        return false; // Character mismatch
                    }
                    if (!$isCaseSensitive && strtoupper($ch)
                    != strtoupper($pathArr[$i])) {
                        return false;  // Character mismatch
                    }
                }
            }
            return true; // String matches against pattern
        }

        if ($patIdxEnd == 0) {
            return true; // Pattern contains only '*', which matches anything
        }
        // Process characters before first star
        while (($ch = $patArr[$patIdxStart]) != '*' && $pathIdxStart <= $pathIdxEnd) {
            if ($ch != '?') {
                if ($isCaseSensitive && $ch != $pathArr[$pathIdxStart]) {
                    return false; // Character mismatch
                }
                if (!$isCaseSensitive && strtoupper($ch)
                != strtoupper($pathArr[$pathIdxStart])) {
                    return false; // Character mismatch
                }
            }
            $patIdxStart++;
            $pathIdxStart++;
        }
        if ($pathIdxStart > $pathIdxEnd) {
            // All characters in the string are used. Check if only '*'s are
            // left in the pattern. If so, we succeeded. Otherwise failure.
            for ($i = $patIdxStart; $i <= $patIdxEnd; $i++) {
                if ($patArr[$i] != '*') {
                    return false;
                }
            }
            return true;
        }

        // Process characters after last star
        while (($ch = $patArr[$patIdxEnd]) != '*' && $pathIdxStart <= $pathIdxEnd) {
            if ($ch != '?') {
                if ($isCaseSensitive && $ch != $pathArr[$pathIdxEnd]) {
                    return false; // Character mismatch
                }
                if (!$isCaseSensitive && strtoupper($ch)
                != strtoupper($pathArr[$pathIdxEnd])) {
                    return false; // Character mismatch
                }
            }
            $patIdxEnd--;
            $pathIdxEnd--;
        }
        if ($pathIdxStart > $pathIdxEnd) {
            // All characters in the string are used. Check if only '*'s are
            // left in the pattern. If so, we succeeded. Otherwise failure.
            for ($i = $patIdxStart; $i <= $patIdxEnd; $i++) {
                if ($patArr[$i] != '*') {
                    return false;
                }
            }
            return true;
        }

        // process pattern between stars. padIdxStart and patIdxEnd point
        // always to a '*'.
        while ($patIdxStart != $patIdxEnd && $pathIdxStart <= $pathIdxEnd) {
            $patIdxTmp = -1;
            for ($i = $patIdxStart + 1; $i <= $patIdxEnd; $i++) {
                if ($patArr[$i] == '*') {
                    $patIdxTmp = $i;
                    break;
                }
            }
            if ($patIdxTmp == $patIdxStart + 1) {
                // Two stars next to each other, skip the first one.
                $patIdxStart++;
                continue;
            }
            // Find the pattern between padIdxStart & padIdxTmp in str between
            // strIdxStart & strIdxEnd
            $patLength = ($patIdxTmp - $patIdxStart - 1);
            $pathLength = ($pathIdxEnd - $pathIdxStart + 1);
            $foundIdx = -1;
            //strLoop:
            for ($i = 0; $i <= $pathLength - $patLength; $i++) {
                for ($j = 0; $j < $patLength; $j++) {
                    $ch = $patArr[$patIdxStart + $j + 1];
                    if ($ch != '?') {
                        if ($isCaseSensitive && $ch != $pathArr[$pathIdxStart + $i
                        + $j]) {
                            continue 2;
                            //continue strLoop;
                        }
                        if (!$isCaseSensitive
                        && strtoupper($ch)
                        != strtoupper($pathArr[$pathIdxStart + $i + $j])) {
                            continue 2;
                            //continue strLoop;
                        }
                    }
                }
                $foundIdx = $pathIdxStart + $i;
                break;
            }

            if ($foundIdx == -1) {
                return false;
            }

            $patIdxStart = $patIdxTmp;
            $pathIdxStart = $foundIdx + $patLength;
        }

        // All characters in the string are used. Check if only '*'s are left
        // in the pattern. If so, we succeeded. Otherwise failure.
        for ($i = $patIdxStart; $i <= $patIdxEnd; $i++) {
            if ($patArr[$i] != '*') {
                return false;
            }
        }

        return true;
        
    }

}

?>