<?php

namespace Larawatch\Checks;

use Illuminate\Support\Facades\Process;

class RepoVersionCheck extends BaseCheck
{
    public function run(): CheckResult
    {
        $this->setStartTime(null);

        $gitLog = Process::run("git log --decorate --oneline --pretty='%h refs: %d message:%s'");
        $gitLogOutput = $fullGitLogOutput = $gitLog->output();
        $startPoint = strpos($gitLogOutput, 'HEAD -> ');
        $findComma = strpos($gitLogOutput, ")", $startPoint+8);
        $branchName = substr($gitLogOutput,$startPoint+8,($findComma-($startPoint+8)));
        $result = CheckResult::make()
            ->startTime($this->getStartTime())
            ->resultData([
                'gitCommit' => $fullGitLogOutput, 
                'branchName' => $branchName,
            ]);
            
        return $result->ok();
    }

}
