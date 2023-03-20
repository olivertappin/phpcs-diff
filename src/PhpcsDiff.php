<?php

namespace PhpcsDiff;

use League\CLImate\CLImate;
use PhpcsDiff\Filter\Exception\FilterException;
use PhpcsDiff\Filter\Filter;
use PhpcsDiff\Filter\Rule\HasMessagesRule;
use PhpcsDiff\Filter\Rule\PhpFileRule;
use PhpcsDiff\Mapper\PhpcsViolationsMapper;

class PhpcsDiff
{
    /**
     * @var array
     */
    protected $argv = [];

    /**
     * @var CLImate
     */
    protected $climate;

    /**
     * @var bool
     */
    protected $isVerbose = false;

    /**
     * @var int
     */
    protected $exitCode = 0;

    /**
     * @var string
     */
    protected $baseBranch;

    /**
     * @var string
     */
    protected $currentBranch = '';

    /**
     * @var string
     */
    protected $standard = 'ruleset.xml';

    /**
     * @param array $argv
     * @param CLImate $climate
     */
    public function __construct(array $argv, CLImate $climate)
    {
        $this->argv = $argv;
        $this->climate = $climate;

        if ($this->getFlag('-v')) {
            $this->climate->comment('Running in verbose mode.');
            $this->isVerbose = true;
        }

        $standard = $this->getFlag('--standard');
        if (!empty($standard) && is_string($standard)) {
            $this->climate->comment('Custom standard: ' . $standard . PHP_EOL);

            $this->standard = $standard;
        }

        if (!isset($this->argv[1])) {
            $this->error('Please provide a <bold>base branch</bold> as the first argument.');
            return;
        }

        $this->baseBranch = 'origin/' . str_replace('origin/', '', $this->argv[1]);
        $this->currentBranch = trim(shell_exec('git rev-parse --verify HEAD'));

        if (empty($this->currentBranch)) {
            $this->error('Unable to get <bold>current</bold> branch.');
        }
    }

    /**
     * @param string $flag
     * @return bool|string
     */
    protected function getFlag(string $flag)
    {
        $return = false;
        $argv = $this->argv;

        foreach ($argv as $key => $arg) {
            if (strtok($arg, '=') === $flag) {
                $return = !empty(strrchr($arg, '=')) ? substr(strrchr($arg, '='), 1) : true;

                unset($argv[$key]);

                break;
            }
        }

        if ($return) {
            $this->argv = array_values($argv);
        }

        return $return;
    }

    /**
     * @param int $exitCode
     */
    protected function setExitCode(int $exitCode)
    {
        $this->exitCode = $exitCode;
    }

    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    /**
     * @todo Automatically look at server envs for the travis base branch, if not provided?
     */
    public function run(): void
    {
        try {
            $filter = new Filter([new PhpFileRule()], $this->getChangedFiles());
        } catch (FilterException $exception) {
            $this->error($exception->getMessage());
            return;
        }

        $fileDiff = $filter->filter()->getFilteredData();

        if (empty($fileDiff)) {
            $this->climate->info('No difference to compare.');
            return;
        }

        if ($this->isVerbose) {
            $fileDiffCount = count($fileDiff);
            $this->climate->comment(
                'Checking ' . $fileDiffCount . ' ' .
                ngettext('file', 'files', $fileDiffCount) . ' for violations.'
            );
        }

        $phpcsOutput = $this->runPhpcs($fileDiff);

        if (is_null($phpcsOutput)) {
            $this->error('Unable to run phpcs executable.');
            return;
        }

        if ($this->isVerbose) {
            $this->climate->comment('Filtering phpcs output.');
        }

        try {
            $filter = new Filter([new HasMessagesRule()], $phpcsOutput['files']);
        } catch (FilterException $exception) {
            $this->error($exception->getMessage());
            return;
        }

        $files = $filter->filter()->getFilteredData();

        if ($this->isVerbose) {
            $this->climate->comment('Getting changed lines from git diff.');
        }

        $changedLinesPerFile = $this->getChangedLinesPerFile($files);

        if ($this->isVerbose) {
            $this->climate->comment('Comparing phpcs output with changes lines from git diff.');
        }

        $violations = (new PhpcsViolationsMapper(
            $changedLinesPerFile,
            getcwd()
        ))->map($files);

        if ($this->isVerbose) {
            $this->climate->comment('Preparing report.');
        }

        if (empty($violations)) {
            $this->climate->info('No violations to report.');
            return;
        }

        $this->outputViolations($violations);
    }

    /**
     * Run phpcs on a list of files passed into the method
     *
     * @param array $files
     * @return mixed
     */
    protected function runPhpcs(array $files = [])
    {
        $exec = null;
        $root = dirname(__DIR__);

        $locations = [
            'vendor/bin/phpcs',
            $root . '/../../bin/phpcs',
            $root . '/../bin/phpcs',
            $root . '/bin/phpcs',
            $root . '/vendor/bin/phpcs',
            '~/.config/composer/vendor/bin/phpcs',
            '~/.composer/vendor/bin/phpcs',
        ];

        foreach ($locations as $location) {
            if (is_file($location)) {
                $exec = $location;
                break;
            }
        }

        if (!$exec) {
            return null;
        }

        if ($this->isVerbose) {
            $this->climate->info('Using phpcs executable: ' . $exec);
        }

        $exec = PHP_BINARY . ' ' . $exec;
        $command = $exec . ' --report=json --standard=' . $this->standard . ' ' . implode(' ', $files);
        $output = shell_exec($command);

        if ($this->isVerbose) {
            $this->climate->info('Running: ' . $command);
        }

        $json = $output ? json_decode($output, true) : null;
        if ($json === null && $output) {
            $this->climate->error($output);
        }

        return $json;
    }

    /**
     * @param array $output
     */
    protected function outputViolations(array $output): void
    {
        $this->climate->flank(strtoupper('Start of phpcs check'), '#', 10)->br();
        $this->climate->out(implode(PHP_EOL, $output));
        $this->climate->flank(strtoupper('End of phpcs check'), '#', 11)->br();

        $this->error('Violations have been reported.');
    }

    /**
     * Returns a list of files which are within the diff based on the current branch
     *
     * @return array
     */
    protected function getChangedFiles(): array
    {
        // Get a list of changed files (not including deleted files)
        $output = shell_exec(
            'git diff ' . $this->baseBranch . ' ' . $this->currentBranch . ' --name-only --diff-filter=ACM'
        );

        // Convert files into an array
        $output = explode(PHP_EOL, $output);

        // Remove any empty values
        return array_filter($output);
    }

    /**
     * Extract the changed lines for each file from the git diff output
     *
     * @param array $files
     * @return array
     */
    protected function getChangedLinesPerFile(array $files): array
    {
        $extract = [];
        $pattern = [
            'basic' => '^@@ (.*) @@',
            'specific' => '@@ -[0-9]+(?:,[0-9]+)? \+([0-9]+)(?:,([0-9]+))? @@',
        ];

        foreach ($files as $file => $data) {
            $command = 'git diff -U0 ' . $this->baseBranch . ' ' . $this->currentBranch . ' ' . $file .
                ' | grep -E ' . escapeshellarg($pattern['basic']);

            $lineDiff = shell_exec($command);
            $lines = array_filter(explode(PHP_EOL, $lineDiff));
            $linesChanged = [];

            foreach ($lines as $line) {
                preg_match('/' . $pattern['specific'] . '/', $line, $matches);

                // If there were no specific matches, skip this line
                if ([] === $matches) {
                    continue;
                }

                $start = $end = (int)$matches[1];

                // Multiple lines were changed, so we need to calculate the end line
                if (isset($matches[2])) {
                    $length = (int)$matches[2];
                    $end = $start + $length - 1;
                }

                foreach (range($start, $end) as $l) {
                    $linesChanged[$l] = null;
                }
            }

            $extract[$file] = array_keys($linesChanged);
        }

        return $extract;
    }

    /**
     * @param string $message
     * @param int $exitCode
     */
    protected function error(string $message, int $exitCode = 1): void
    {
        $this->climate->error($message);
        $this->setExitCode($exitCode);
    }
}
