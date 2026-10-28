<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;

class AiCandidateMatchingService
{
    /**
     * Score a candidate application against a job specification.
     * Returns an array with ['score' => integer (0-100), 'summary' => string, 'highlights' => array].
     */
    public static function evaluate(Job $job, User $candidate, string $coverLetter): array
    {
        $jobText = strtolower($job->title . ' ' . $job->description . ' ' . $job->requirements . ' ' . $job->main_category);
        $candidateText = strtolower($coverLetter . ' ' . ($candidate->headline ?? '') . ' ' . ($candidate->skills ?? '') . ' ' . ($candidate->experience ?? ''));

        // Core tech & domain keywords extraction
        $keywords = [
            'laravel', 'php', 'mysql', 'sql', 'sqlite', 'postgresql', 'javascript', 'typescript', 'react', 'vue',
            'api', 'rest', 'git', 'docker', 'aws', 'html', 'css', 'tailwind', 'bootstrap', 'qa', 'testing',
            'accounting', 'finance', 'banking', 'audit', 'tax', 'sales', 'marketing', 'seo', 'content',
            'design', 'figma', 'ui', 'ux', 'communication', 'leadership', 'management', 'support', 'customer'
        ];

        $matchedKeywords = [];
        $totalJobKeywords = 0;

        foreach ($keywords as $keyword) {
            if (str_contains($jobText, $keyword)) {
                $totalJobKeywords++;
                if (str_contains($candidateText, $keyword)) {
                    $matchedKeywords[] = ucfirst($keyword);
                }
            }
        }

        // Base score calculation
        if ($totalJobKeywords > 0) {
            $keywordScore = (count($matchedKeywords) / $totalJobKeywords) * 60;
        } else {
            $keywordScore = 45;
        }

        // Quality check on cover letter length & profile completion
        $qualityScore = min(strlen($coverLetter) / 25, 25);
        $profileBonus = (!empty($candidate->skills) ? 8 : 0) + (!empty($candidate->experience) ? 7 : 0);

        $finalScore = (int) round(min(max($keywordScore + $qualityScore + $profileBonus, 45), 98));

        // Generate Executive AI Summary
        $highlightStr = !empty($matchedKeywords) ? implode(', ', array_slice($matchedKeywords, 0, 4)) : 'Relevant domain background';
        
        $assessment = match (true) {
            $finalScore >= 85 => "Exceptional Match ({$finalScore}%): Highly qualified candidate demonstrating strong alignment with {$highlightStr}. Recommended for immediate technical interview.",
            $finalScore >= 70 => "Strong Candidate ({$finalScore}%): Solid potential with demonstrated skills in {$highlightStr}. Well suited for initial screening.",
            default => "Moderate Match ({$finalScore}%): Candidate meets essential baseline criteria with basic proficiency in {$highlightStr}."
        };

        return [
            'score' => $finalScore,
            'summary' => $assessment,
            'matched_skills' => $matchedKeywords,
        ];
    }
}
