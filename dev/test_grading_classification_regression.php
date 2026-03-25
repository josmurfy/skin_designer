<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

function classifyIsGraded(string $title, string $condition, ?string $explicitGraded = null): bool {
    $graderRegex = '/\b(PSA|BGS|BGSX|SGC|CSA|HGA|GAI|ACE|CGC|KSA)\b/i';

    $hasGraderMention = preg_match($graderRegex, $title) === 1;
    $hasGraderAndScore = preg_match('/\b(PSA|BGS|BGSX|SGC|CSA|HGA|GAI|ACE|CGC|KSA)\s+(\d{1,2}(?:\.\d)?)\b/i', $title) === 1;

    $conditionText = strtolower(trim($condition));
    $isConditionUngraded = ($conditionText === 'ungraded');
    $isExplicitUngraded = $isConditionUngraded || stripos($title, 'ungraded') !== false || stripos($title, 'not graded') !== false;
    $isConditionGraded = ($conditionText === 'graded');

    $isGraded = !$isExplicitUngraded && ($hasGraderMention || $hasGraderAndScore);

    if ($isConditionGraded && !$hasGraderMention) {
        $isGraded = false;
    }

    if ($explicitGraded !== null) {
        $flag = strtolower(trim($explicitGraded));
        if (in_array($flag, ['yes', 'y', 'true', '1', 'graded'], true)) {
            $isGraded = true;
        } elseif (in_array($flag, ['no', 'n', 'false', '0', 'ungraded', 'not graded'], true)) {
            $isGraded = false;
        }
    }

    return $isGraded;
}

$cases = [
    [
        'name' => 'condition graded but no grader mention => raw',
        'title' => 'HTF Teemu Selanne The Leaf Set 1993-94 card #13',
        'condition' => 'Graded',
        'expected' => false,
    ],
    [
        'name' => 'grader mention in title => graded',
        'title' => 'Wayne Gretzky PSA 9 OPC',
        'condition' => 'Graded',
        'expected' => true,
    ],
    [
        'name' => 'condition ungraded => raw',
        'title' => 'Mario Lemieux rookie',
        'condition' => 'Ungraded',
        'expected' => false,
    ],
];

$failed = 0;

foreach ($cases as $case) {
    $actual = classifyIsGraded($case['title'], $case['condition']);
    $ok = ($actual === $case['expected']);

    echo ($ok ? '[OK] ' : '[FAIL] ') . $case['name'] . PHP_EOL;

    if (!$ok) {
        echo '  title=' . $case['title'] . PHP_EOL;
        echo '  condition=' . $case['condition'] . PHP_EOL;
        echo '  expected=' . ($case['expected'] ? 'graded' : 'raw') . ' actual=' . ($actual ? 'graded' : 'raw') . PHP_EOL;
        $failed++;
    }
}

if ($failed > 0) {
    exit(1);
}

echo 'All regression cases passed.' . PHP_EOL;
