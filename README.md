This is the Upload Groups block for Moodle 2.8 and up.

Created at University of Minnesota by the Custom Solutions team.

To install using git, type this command in the root of your Moodle install
    git clone git@github.com:at-tools/block_uploadgroups.git

Alternatively, download the zip from
    https://github.com/at-tools/block_uploadgroups/zipball/master
unzip it into the blocks folder, and then rename the new folder to upload_group.

Once installed, capability "block/uploadgroups:add" needs to be added to the roles/users (e.g. teacher) in order for them to be able to use the block.

RELEASE NOTE
[2016010401]
- Fixes the currently enrolled user check (Thanks to Longfei Yu from UMass)

[2016010400]
- Code style changes required by Moodle for submission to plugins directory

[2015120100]
- initial release
